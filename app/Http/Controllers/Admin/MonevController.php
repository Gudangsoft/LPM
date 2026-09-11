<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Monev;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MonevController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:monev.view', only: ['index', 'show']),
            new Middleware('permission:monev.manage', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    /**
     * Index is not scoped to the kaprodi's own prodi - unlike RpsReview,
     * kaprodi may view every prodi's (and institution-level) monev entries,
     * they just can't create/edit/delete outside their own prodi.
     */
    public function index(Request $request)
    {
        $query = Monev::with('prodi')
            ->when($request->filled('prodi_id'), fn ($q) => $q->where('prodi_id', $request->prodi_id))
            ->when($request->filled('tahun_akademik'), fn ($q) => $q->where('tahun_akademik', $request->tahun_akademik))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('tanggal_monev');

        $monev = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => (clone $query)->count(),
            'baik' => (clone $query)->where('status', 'baik')->count(),
            'cukup' => (clone $query)->where('status', 'cukup')->count(),
            'kurang' => (clone $query)->where('status', 'kurang')->count(),
        ];

        $prodiOptions = Prodi::orderBy('nama')->get();
        $tahunOptions = Monev::query()->distinct()->orderByDesc('tahun_akademik')->pluck('tahun_akademik');

        return view('admin.ami.monev.index', compact('monev', 'stats', 'prodiOptions', 'tahunOptions'));
    }

    public function create()
    {
        $prodiOptions = $this->prodiOptions();

        return view('admin.ami.monev.create', compact('prodiOptions'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $this->assertProdiAllowed($validated['prodi_id'] ?? null);

        Monev::create($validated);

        return redirect()->route('admin.ami.monev.index')->with('success', 'Data monev berhasil ditambahkan.');
    }

    public function edit(Monev $monev)
    {
        $this->assertProdiAllowed($monev->prodi_id);
        $prodiOptions = $this->prodiOptions();

        return view('admin.ami.monev.edit', compact('monev', 'prodiOptions'));
    }

    public function update(Request $request, Monev $monev)
    {
        $this->assertProdiAllowed($monev->prodi_id);

        $validated = $this->validated($request);
        $this->assertProdiAllowed($validated['prodi_id'] ?? null);

        $monev->update($validated);

        return redirect()->route('admin.ami.monev.index')->with('success', 'Data monev berhasil diperbarui.');
    }

    public function destroy(Monev $monev)
    {
        $this->assertProdiAllowed($monev->prodi_id);

        $monev->delete();

        return redirect()->route('admin.ami.monev.index')->with('success', 'Data monev dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'prodi_id' => 'nullable|exists:prodi,id',
            'tahun_akademik' => 'required|string|max:20',
            'semester' => 'required|in:ganjil,genap',
            'aspek_monev' => 'required|string|max:255',
            'hasil' => 'required|string',
            'tanggal_monev' => 'required|date',
            'petugas_monev' => 'nullable|string|max:255',
            'status' => 'required|in:baik,cukup,kurang',
            'tindak_lanjut' => 'nullable|string',
        ]);
    }

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
            'Anda hanya dapat mengelola monev program studi Anda sendiri.'
        );
    }
}
