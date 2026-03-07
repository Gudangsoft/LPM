<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akreditasi;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AkreditasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Akreditasi::with('prodi')->latest();
        
        if ($request->has('search') && $request->search) {
            $query->whereHas('prodi', function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('lembaga') && $request->lembaga) {
            $query->where('lembaga', $request->lembaga);
        }
        
        $akreditasi = $query->paginate(15);
        $statusOptions = ['aktif', 'kadaluarsa', 'proses_perpanjangan'];
        $lembagaOptions = ['BAN-PT', 'LAM-PTKes', 'LAM-PTIK', 'LAM-Teknik', 'Internasional'];
        
        return view('admin.akreditasi.index', compact('akreditasi', 'statusOptions', 'lembagaOptions'));
    }

    public function create()
    {
        $prodis = Prodi::active()->orderBy('nama')->get();
        $lembagaOptions = ['BAN-PT', 'LAM-PTKes', 'LAM-PTIK', 'LAM-Teknik', 'Internasional'];
        $peringkatOptions = ['A', 'B', 'C', 'Unggul', 'Baik Sekali', 'Baik'];
        
        return view('admin.akreditasi.create', compact('prodis', 'lembagaOptions', 'peringkatOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prodi_id' => 'required|exists:prodi,id',
            'lembaga' => 'required|string|max:100',
            'peringkat' => 'required|string|max:50',
            'nomor_sk' => 'required|string|max:255',
            'tanggal_sk' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date|after:tanggal_sk',
            'file_sk' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:aktif,kadaluarsa,proses_perpanjangan',
            'catatan' => 'nullable|string',
        ]);

        if ($request->hasFile('file_sk')) {
            $validated['file_sk'] = $request->file('file_sk')->store('akreditasi', 'public');
        }

        Akreditasi::create($validated);

        return redirect()->route('admin.akreditasi.index')
            ->with('success', 'Data akreditasi berhasil ditambahkan.');
    }

    public function show(Akreditasi $akreditasi)
    {
        $akreditasi->load('prodi');
        
        return view('admin.akreditasi.show', compact('akreditasi'));
    }

    public function edit(Akreditasi $akreditasi)
    {
        $prodis = Prodi::active()->orderBy('nama')->get();
        $lembagaOptions = ['BAN-PT', 'LAM-PTKes', 'LAM-PTIK', 'LAM-Teknik', 'Internasional'];
        $peringkatOptions = ['A', 'B', 'C', 'Unggul', 'Baik Sekali', 'Baik'];
        
        return view('admin.akreditasi.edit', compact('akreditasi', 'prodis', 'lembagaOptions', 'peringkatOptions'));
    }

    public function update(Request $request, Akreditasi $akreditasi)
    {
        $validated = $request->validate([
            'prodi_id' => 'required|exists:prodi,id',
            'lembaga' => 'required|string|max:100',
            'peringkat' => 'required|string|max:50',
            'nomor_sk' => 'required|string|max:255',
            'tanggal_sk' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date|after:tanggal_sk',
            'file_sk' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:aktif,kadaluarsa,proses_perpanjangan',
            'catatan' => 'nullable|string',
        ]);

        if ($request->hasFile('file_sk')) {
            if ($akreditasi->file_sk) {
                Storage::disk('public')->delete($akreditasi->file_sk);
            }
            $validated['file_sk'] = $request->file('file_sk')->store('akreditasi', 'public');
        }

        $akreditasi->update($validated);

        return redirect()->route('admin.akreditasi.index')
            ->with('success', 'Data akreditasi berhasil diperbarui.');
    }

    public function destroy(Akreditasi $akreditasi)
    {
        if ($akreditasi->file_sk) {
            Storage::disk('public')->delete($akreditasi->file_sk);
        }
        
        $akreditasi->delete();

        return redirect()->route('admin.akreditasi.index')
            ->with('success', 'Data akreditasi berhasil dihapus.');
    }

    /**
     * Dashboard akreditasi dengan statistik
     */
    public function dashboard()
    {
        $stats = [
            'total_prodi' => Prodi::active()->count(),
            'akreditasi_aktif' => Akreditasi::aktif()->count(),
            'akan_kadaluarsa' => Akreditasi::expiringSoon()->count(),
            'sudah_kadaluarsa' => Akreditasi::expired()->count(),
        ];

        $akreditasiExpiring = Akreditasi::with('prodi')
            ->expiringSoon()
            ->orderBy('tanggal_kadaluarsa')
            ->get();

        $akreditasiByPeringkat = Akreditasi::aktif()
            ->selectRaw('peringkat, count(*) as total')
            ->groupBy('peringkat')
            ->get();

        $akreditasiByLembaga = Akreditasi::aktif()
            ->selectRaw('lembaga, count(*) as total')
            ->groupBy('lembaga')
            ->get();

        return view('admin.akreditasi.dashboard', compact(
            'stats',
            'akreditasiExpiring',
            'akreditasiByPeringkat',
            'akreditasiByLembaga'
        ));
    }
}
