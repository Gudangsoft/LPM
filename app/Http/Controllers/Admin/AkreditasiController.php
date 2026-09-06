<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akreditasi;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class AkreditasiController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:akreditasi.view', only: ['index', 'show', 'dashboard']),
            new Middleware('permission:akreditasi.create', only: ['create', 'store']),
            new Middleware('permission:akreditasi.edit', only: ['edit', 'update']),
            new Middleware('permission:akreditasi.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Akreditasi::with('prodi')->latest()
            ->when(!$user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->ownedByKaprodi($user));

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
        $user = auth()->user();
        if (!$user->isAdmin() && $user->isKaprodi()) {
            abort_unless(
                $akreditasi->prodi?->kaprodi_id === $user->id,
                403,
                'Anda hanya dapat melihat data akreditasi program studi Anda sendiri.'
            );
        }

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
        $user = auth()->user();
        $scoped = !$user->isAdmin() && $user->isKaprodi();

        $stats = [
            'total_prodi' => Prodi::active()->when($scoped, fn ($q) => $q->where('kaprodi_id', $user->id))->count(),
            'akreditasi_aktif' => Akreditasi::aktif()->when($scoped, fn ($q) => $q->ownedByKaprodi($user))->count(),
            'akan_kadaluarsa' => Akreditasi::expiringSoon()->when($scoped, fn ($q) => $q->ownedByKaprodi($user))->count(),
            'sudah_kadaluarsa' => Akreditasi::expired()->when($scoped, fn ($q) => $q->ownedByKaprodi($user))->count(),
        ];

        $akreditasiExpiring = Akreditasi::with('prodi')
            ->expiringSoon()
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user))
            ->orderBy('tanggal_kadaluarsa')
            ->get();

        $akreditasiByPeringkat = Akreditasi::aktif()
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user))
            ->selectRaw('peringkat, count(*) as total')
            ->groupBy('peringkat')
            ->get();

        $akreditasiByLembaga = Akreditasi::aktif()
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user))
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
