<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CapaianPembelajaran;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CapaianPembelajaranController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:capaian-pembelajaran.view', only: ['index', 'show']),
            new Middleware('permission:capaian-pembelajaran.manage', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $scoped = ! $user->isAdmin() && $user->isKaprodi();

        $query = CapaianPembelajaran::with('prodi')
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user))
            ->when($request->filled('prodi_id'), fn ($q) => $q->where('prodi_id', $request->prodi_id))
            ->when($request->filled('tahun_akademik'), fn ($q) => $q->where('tahun_akademik', $request->tahun_akademik))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where('mata_kuliah', 'like', "%{$request->search}%"))
            ->orderByDesc('tahun_akademik')->orderBy('mata_kuliah');

        $capaian = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => (clone $query)->count(),
            'tercapai' => (clone $query)->where('status', 'tercapai')->count(),
            'tidak_tercapai' => (clone $query)->where('status', 'tidak_tercapai')->count(),
            'belum' => (clone $query)->where('status', 'belum_dievaluasi')->count(),
        ];

        $prodis = Prodi::when($scoped, fn ($q) => $q->where('kaprodi_id', $user->id))->orderBy('nama')->get();
        $tahunOptions = CapaianPembelajaran::query()->distinct()->orderByDesc('tahun_akademik')->pluck('tahun_akademik');

        return view('admin.ami.capaian-pembelajaran.index', compact('capaian', 'stats', 'prodis', 'tahunOptions'));
    }

    public function create()
    {
        $prodis = $this->prodiOptions();

        return view('admin.ami.capaian-pembelajaran.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $this->assertProdiAllowed($validated['prodi_id']);

        CapaianPembelajaran::create($this->withComputedStatus($validated));

        return redirect()->route('admin.ami.capaian-pembelajaran.index')->with('success', 'Data capaian pembelajaran berhasil ditambahkan.');
    }

    public function edit(CapaianPembelajaran $capaianPembelajaran)
    {
        $this->assertProdiAllowed($capaianPembelajaran->prodi_id);
        $prodis = $this->prodiOptions();

        return view('admin.ami.capaian-pembelajaran.edit', compact('capaianPembelajaran', 'prodis'));
    }

    public function update(Request $request, CapaianPembelajaran $capaianPembelajaran)
    {
        $this->assertProdiAllowed($capaianPembelajaran->prodi_id);

        $validated = $this->validated($request);
        $this->assertProdiAllowed($validated['prodi_id']);

        $capaianPembelajaran->update($this->withComputedStatus($validated));

        return redirect()->route('admin.ami.capaian-pembelajaran.index')->with('success', 'Data capaian pembelajaran berhasil diperbarui.');
    }

    public function destroy(CapaianPembelajaran $capaianPembelajaran)
    {
        $this->assertProdiAllowed($capaianPembelajaran->prodi_id);

        $capaianPembelajaran->delete();

        return redirect()->route('admin.ami.capaian-pembelajaran.index')->with('success', 'Data capaian pembelajaran dihapus.');
    }

    // ---------------------------------------------------------------------

    private function validated(Request $request): array
    {
        return $request->validate([
            'prodi_id' => 'required|exists:prodi,id',
            'mata_kuliah' => 'required|string|max:255',
            'cpmk' => 'required|string|max:50',
            'deskripsi_cpmk' => 'nullable|string',
            'tahun_akademik' => 'required|string|max:20',
            'semester' => 'required|in:ganjil,genap',
            'target_capaian' => 'required|numeric|min:0|max:100',
            'realisasi_capaian' => 'nullable|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);
    }

    /**
     * Status is derived, not chosen: tercapai once realisasi meets target,
     * otherwise tidak_tercapai, or belum_dievaluasi while realisasi is empty.
     */
    private function withComputedStatus(array $data): array
    {
        $data['status'] = $data['realisasi_capaian'] === null
            ? 'belum_dievaluasi'
            : ($data['realisasi_capaian'] >= $data['target_capaian'] ? 'tercapai' : 'tidak_tercapai');

        return $data;
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
                'Anda hanya dapat mengelola capaian pembelajaran program studi Anda sendiri.'
            );
        }
    }
}
