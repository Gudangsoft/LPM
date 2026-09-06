<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemuanAmi;
use App\Models\JadwalAmi;
use App\Models\Auditor;
use App\Models\PeriodeAmi;
use App\Models\Prodi;
use App\Models\StandarMutu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class TemuanAmiController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:temuan.view', only: ['index', 'show']),
            new Middleware('permission:temuan.create', only: ['create', 'store']),
            new Middleware('permission:temuan.edit', only: ['edit', 'update', 'close', 'reopen']),
            new Middleware('permission:temuan.delete', only: ['destroy']),
            new Middleware('permission:temuan.verify', only: ['verify']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = TemuanAmi::with(['jadwalAmi.prodi', 'auditor.user'])
            ->latest()
            ->when(!$user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->ownedByKaprodi($user));

        if ($request->has('jadwal_id') && $request->jadwal_id) {
            $query->where('jadwal_ami_id', $request->jadwal_id);
        }

        if ($request->has('periode_id') && $request->periode_id) {
            $query->whereHas('jadwalAmi', fn ($q) => $q->where('periode_ami_id', $request->periode_id));
        }

        if ($request->has('prodi_id') && $request->prodi_id) {
            $query->whereHas('jadwalAmi', fn ($q) => $q->where('prodi_id', $request->prodi_id));
        }

        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $temuans = $query->paginate(15);
        $periodes = PeriodeAmi::orderByDesc('tanggal_mulai')->get();
        $prodis = Prodi::active()->orderBy('nama')->get();
        $kategoriOptions = ['mayor', 'minor', 'observasi', 'rekomendasi'];
        $statusOptions = ['open', 'in_progress', 'closed', 'verified'];

        return view('admin.ami.temuan.index', compact('temuans', 'periodes', 'prodis', 'kategoriOptions', 'statusOptions'));
    }

    public function create(Request $request)
    {
        $jadwalId = $request->get('jadwal_id');
        $jadwal = $jadwalId ? JadwalAmi::with('prodi')->find($jadwalId) : null;
        
        $jadwals = JadwalAmi::with('prodi')
            ->whereIn('status', ['berlangsung', 'selesai'])
            ->orderByDesc('tanggal_audit')
            ->get();
        
        // Get auditors assigned to the selected jadwal or all active auditors
        $auditors = $jadwal 
            ? $jadwal->penugasan()->with('auditor.user')->get()->pluck('auditor')
            : Auditor::aktif()->with('user')->get();
        
        $kategoriOptions = ['mayor', 'minor', 'observasi', 'rekomendasi'];
        $standarMutus = StandarMutu::active()->ordered()->get();

        return view('admin.ami.temuan.create', compact(
            'jadwal',
            'jadwals',
            'auditors',
            'kategoriOptions',
            'standarMutus'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_ami_id' => 'required|exists:jadwal_ami,id',
            'auditor_id' => 'required|exists:auditor,id',
            'standar_mutu_id' => 'required|exists:standar_mutu,id',
            'kategori' => 'required|in:mayor,minor,observasi,rekomendasi',
            'deskripsi' => 'required|string',
            'bukti' => 'nullable|string',
            'bukti_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'akar_masalah' => 'nullable|string',
            'rekomendasi' => 'required|string',
            'batas_tindak_lanjut' => 'nullable|date',
            'status' => 'required|in:open,in_progress,closed,verified',
        ]);

        $validated['standar'] = StandarMutu::findOrFail($validated['standar_mutu_id'])->nama;

        if ($request->hasFile('bukti_file')) {
            $validated['bukti_file'] = $request->file('bukti_file')->store('temuan', 'public');
        }

        $temuan = TemuanAmi::create($validated);

        return redirect()->route('admin.ami.temuan.show', $temuan)
            ->with('success', 'Temuan berhasil ditambahkan.');
    }

    public function show(TemuanAmi $temuan)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $user->isKaprodi()) {
            abort_unless(
                $temuan->jadwalAmi?->prodi?->kaprodi_id === $user->id,
                403,
                'Anda hanya dapat melihat temuan program studi Anda sendiri.'
            );
        }

        $temuan->load([
            'jadwalAmi.prodi',
            'auditor.user',
            'tindakLanjut.user',
            'tindakLanjut.reviewer',
        ]);
        
        return view('admin.ami.temuan.show', compact('temuan'));
    }

    public function edit(TemuanAmi $temuan)
    {
        $jadwals = JadwalAmi::with('prodi')
            ->whereIn('status', ['berlangsung', 'selesai'])
            ->orderByDesc('tanggal_audit')
            ->get();
        
        $auditors = $temuan->jadwalAmi->penugasan()->with('auditor.user')->get()->pluck('auditor');
        
        $kategoriOptions = ['mayor', 'minor', 'observasi', 'rekomendasi'];
        $standarMutus = StandarMutu::active()->ordered()->get();

        return view('admin.ami.temuan.edit', compact(
            'temuan',
            'jadwals',
            'auditors',
            'kategoriOptions',
            'standarMutus'
        ));
    }

    public function update(Request $request, TemuanAmi $temuan)
    {
        $validated = $request->validate([
            'jadwal_ami_id' => 'required|exists:jadwal_ami,id',
            'auditor_id' => 'required|exists:auditor,id',
            'standar_mutu_id' => 'required|exists:standar_mutu,id',
            'kategori' => 'required|in:mayor,minor,observasi,rekomendasi',
            'deskripsi' => 'required|string',
            'bukti' => 'nullable|string',
            'bukti_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'akar_masalah' => 'nullable|string',
            'rekomendasi' => 'required|string',
            'batas_tindak_lanjut' => 'nullable|date',
            'status' => 'required|in:open,in_progress,closed,verified',
        ]);

        $validated['standar'] = StandarMutu::findOrFail($validated['standar_mutu_id'])->nama;

        if ($request->hasFile('bukti_file')) {
            if ($temuan->bukti_file) {
                Storage::disk('public')->delete($temuan->bukti_file);
            }
            $validated['bukti_file'] = $request->file('bukti_file')->store('temuan', 'public');
        }

        $temuan->update($validated);

        return redirect()->route('admin.ami.temuan.show', $temuan)
            ->with('success', 'Temuan berhasil diperbarui.');
    }

    public function destroy(TemuanAmi $temuan)
    {
        if ($temuan->tindakLanjut()->count() > 0) {
            return redirect()->route('admin.ami.temuan.index')
                ->with('error', 'Temuan tidak dapat dihapus karena sudah ada tindak lanjut.');
        }

        if ($temuan->bukti_file) {
            Storage::disk('public')->delete($temuan->bukti_file);
        }

        $temuan->delete();

        return redirect()->route('admin.ami.temuan.index')
            ->with('success', 'Temuan berhasil dihapus.');
    }

    /**
     * Verify a temuan
     */
    public function verify(TemuanAmi $temuan)
    {
        $temuan->verify();

        return back()->with('success', 'Temuan berhasil diverifikasi.');
    }

    /**
     * Close a temuan
     */
    public function close(TemuanAmi $temuan)
    {
        $temuan->close();

        return back()->with('success', 'Temuan berhasil ditutup.');
    }

    /**
     * Reopen a temuan
     */
    public function reopen(TemuanAmi $temuan)
    {
        $temuan->update(['status' => 'in_progress']);

        return back()->with('success', 'Temuan berhasil dibuka kembali.');
    }
}
