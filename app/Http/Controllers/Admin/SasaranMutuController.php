<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\SasaranMutu;
use App\Models\StandarMutu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SasaranMutuController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:sasaran-mutu.view', only: ['index', 'show']),
            new Middleware('permission:sasaran-mutu.manage', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = SasaranMutu::with(['standarMutu', 'prodi'])
            ->when($request->filled('standar_mutu_id'), fn ($q) => $q->where('standar_mutu_id', $request->standar_mutu_id))
            ->when($request->filled('prodi_id'), fn ($q) => $q->where('prodi_id', $request->prodi_id))
            ->when($request->filled('tahun_akademik'), fn ($q) => $q->where('tahun_akademik', $request->tahun_akademik))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('tahun_akademik');

        $sasaran = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => (clone $query)->count(),
            'tercapai' => (clone $query)->where('status', 'tercapai')->count(),
            'tidak_tercapai' => (clone $query)->where('status', 'tidak_tercapai')->count(),
            'belum' => (clone $query)->where('status', 'belum_dievaluasi')->count(),
        ];

        $standarOptions = StandarMutu::orderBy('urutan')->get();
        $prodiOptions = Prodi::orderBy('nama')->get();
        $tahunOptions = SasaranMutu::query()->distinct()->orderByDesc('tahun_akademik')->pluck('tahun_akademik');

        return view('admin.ami.sasaran-mutu.index', compact('sasaran', 'stats', 'standarOptions', 'prodiOptions', 'tahunOptions'));
    }

    public function create()
    {
        $standarOptions = StandarMutu::orderBy('urutan')->get();
        $prodiOptions = $this->prodiOptions();

        return view('admin.ami.sasaran-mutu.create', compact('standarOptions', 'prodiOptions'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $this->assertProdiAllowed($validated['prodi_id'] ?? null);

        SasaranMutu::create($validated);

        return redirect()->route('admin.ami.sasaran-mutu.index')->with('success', 'Sasaran mutu berhasil ditambahkan.');
    }

    public function edit(SasaranMutu $sasaranMutu)
    {
        $this->assertProdiAllowed($sasaranMutu->prodi_id);
        $standarOptions = StandarMutu::orderBy('urutan')->get();
        $prodiOptions = $this->prodiOptions();

        return view('admin.ami.sasaran-mutu.edit', compact('sasaranMutu', 'standarOptions', 'prodiOptions'));
    }

    public function update(Request $request, SasaranMutu $sasaranMutu)
    {
        $this->assertProdiAllowed($sasaranMutu->prodi_id);

        $validated = $this->validated($request);
        $this->assertProdiAllowed($validated['prodi_id'] ?? null);

        $sasaranMutu->update($validated);

        return redirect()->route('admin.ami.sasaran-mutu.index')->with('success', 'Sasaran mutu berhasil diperbarui.');
    }

    public function destroy(SasaranMutu $sasaranMutu)
    {
        $this->assertProdiAllowed($sasaranMutu->prodi_id);

        $sasaranMutu->delete();

        return redirect()->route('admin.ami.sasaran-mutu.index')->with('success', 'Sasaran mutu dihapus.');
    }

    // ---------------------------------------------------------------------

    private function validated(Request $request): array
    {
        return $request->validate([
            'standar_mutu_id' => 'required|exists:standar_mutu,id',
            'prodi_id' => 'nullable|exists:prodi,id',
            'tahun_akademik' => 'required|string|max:20',
            'uraian_sasaran' => 'required|string',
            'indikator' => 'nullable|string|max:255',
            'target' => 'nullable|string|max:255',
            'satuan' => 'nullable|string|max:50',
            'realisasi' => 'nullable|string|max:255',
            'status' => 'nullable|in:belum_dievaluasi,tercapai,tidak_tercapai',
            'keterangan' => 'nullable|string',
        ]);
    }

    /**
     * Kaprodi may only manage rows scoped to their own prodi; institutional
     * targets (prodi_id null) are admin-only.
     */
    private function prodiOptions()
    {
        $user = auth()->user();

        return Prodi::when(! $user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->where('kaprodi_id', $user->id))
            ->orderBy('nama')->get();
    }

    private function assertProdiAllowed(?int $prodiId): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        abort_unless(
            $prodiId !== null && $user->isKaprodi() && Prodi::where('id', $prodiId)->where('kaprodi_id', $user->id)->exists(),
            403,
            'Anda hanya dapat mengelola sasaran mutu program studi Anda sendiri.'
        );
    }
}
