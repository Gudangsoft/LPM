<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluasiPembelajaran;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EvaluasiPembelajaranController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:evaluasi-pembelajaran.view', only: ['index', 'show']),
            new Middleware('permission:evaluasi-pembelajaran.manage', only: ['create', 'store', 'edit', 'update', 'destroy', 'evaluasi']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $scoped = ! $user->isAdmin() && $user->isKaprodi();

        $query = EvaluasiPembelajaran::with(['prodi', 'evaluator'])
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user))
            ->when($request->filled('prodi_id'), fn ($q) => $q->where('prodi_id', $request->prodi_id))
            ->when($request->filled('tahun_akademik'), fn ($q) => $q->where('tahun_akademik', $request->tahun_akademik))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($qq) use ($request) {
                $qq->where('mata_kuliah', 'like', "%{$request->search}%")
                    ->orWhere('dosen_pengampu', 'like', "%{$request->search}%");
            }))
            ->orderByDesc('tahun_akademik')->orderBy('mata_kuliah');

        $evaluasi = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => (clone $query)->count(),
            'dievaluasi' => (clone $query)->where('status', 'dievaluasi')->count(),
            'belum' => (clone $query)->where('status', 'belum_dievaluasi')->count(),
        ];

        $prodis = Prodi::when($scoped, fn ($q) => $q->where('kaprodi_id', $user->id))->orderBy('nama')->get();
        $tahunOptions = EvaluasiPembelajaran::query()->distinct()->orderByDesc('tahun_akademik')->pluck('tahun_akademik');

        return view('admin.ami.evaluasi-pembelajaran.index', compact('evaluasi', 'stats', 'prodis', 'tahunOptions'));
    }

    public function create()
    {
        $prodis = $this->prodiOptions();

        return view('admin.ami.evaluasi-pembelajaran.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $this->assertProdiAllowed($validated['prodi_id']);

        EvaluasiPembelajaran::create($validated);

        return redirect()->route('admin.ami.evaluasi-pembelajaran.index')->with('success', 'Data evaluasi pembelajaran berhasil ditambahkan.');
    }

    public function edit(EvaluasiPembelajaran $evaluasiPembelajaran)
    {
        $this->assertProdiAllowed($evaluasiPembelajaran->prodi_id);
        $prodis = $this->prodiOptions();

        return view('admin.ami.evaluasi-pembelajaran.edit', compact('evaluasiPembelajaran', 'prodis'));
    }

    public function update(Request $request, EvaluasiPembelajaran $evaluasiPembelajaran)
    {
        $this->assertProdiAllowed($evaluasiPembelajaran->prodi_id);

        $validated = $this->validated($request);
        $this->assertProdiAllowed($validated['prodi_id']);

        $evaluasiPembelajaran->update($validated);

        return redirect()->route('admin.ami.evaluasi-pembelajaran.index')->with('success', 'Data evaluasi pembelajaran berhasil diperbarui.');
    }

    public function destroy(EvaluasiPembelajaran $evaluasiPembelajaran)
    {
        $this->assertProdiAllowed($evaluasiPembelajaran->prodi_id);

        $evaluasiPembelajaran->delete();

        return redirect()->route('admin.ami.evaluasi-pembelajaran.index')->with('success', 'Data evaluasi pembelajaran dihapus.');
    }

    /**
     * Record the actual evaluation outcome (kesesuaian RPS, kendala, rekomendasi).
     */
    public function evaluasi(Request $request, EvaluasiPembelajaran $evaluasiPembelajaran)
    {
        $this->assertProdiAllowed($evaluasiPembelajaran->prodi_id);

        $data = $request->validate([
            'kesesuaian_rps' => 'required|in:sesuai,kurang_sesuai,tidak_sesuai',
            'kendala' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
        ]);

        $evaluasiPembelajaran->update([
            ...$data,
            'status' => 'dievaluasi',
            'dievaluasi_oleh' => auth()->id(),
            'dievaluasi_pada' => now(),
        ]);

        return back()->with('success', 'Evaluasi pembelajaran tersimpan.');
    }

    // ---------------------------------------------------------------------

    private function validated(Request $request): array
    {
        return $request->validate([
            'prodi_id' => 'required|exists:prodi,id',
            'mata_kuliah' => 'required|string|max:255',
            'dosen_pengampu' => 'required|string|max:255',
            'tahun_akademik' => 'required|string|max:20',
            'semester' => 'required|in:ganjil,genap',
        ]);
    }

    private function prodiOptions()
    {
        $user = auth()->user();

        return Prodi::when(! $user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->where('kaprodi_id', $user->id))
            ->orderBy('nama')->get();
    }

    private function assertProdiAllowed(int $prodiId): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isKaprodi()) {
            abort_unless(
                Prodi::where('id', $prodiId)->where('kaprodi_id', $user->id)->exists(),
                403,
                'Anda hanya dapat mengelola evaluasi pembelajaran program studi Anda sendiri.'
            );
        }
    }
}
