<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\RpsReview;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RpsReviewController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:rps.view', only: ['index', 'show']),
            new Middleware('permission:rps.manage', only: ['create', 'store', 'edit', 'update', 'destroy', 'review']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $scoped = ! $user->isAdmin() && $user->isKaprodi();

        $query = RpsReview::with(['prodi', 'reviewer'])
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user))
            ->when($request->filled('prodi_id'), fn ($q) => $q->where('prodi_id', $request->prodi_id))
            ->when($request->filled('tahun_akademik'), fn ($q) => $q->where('tahun_akademik', $request->tahun_akademik))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($qq) use ($request) {
                $qq->where('mata_kuliah', 'like', "%{$request->search}%")
                    ->orWhere('dosen_pengampu', 'like', "%{$request->search}%");
            }))
            ->orderByDesc('tahun_akademik')->orderBy('mata_kuliah');

        $rps = $query->paginate(15)->withQueryString();

        $prodis = Prodi::when($scoped, fn ($q) => $q->where('kaprodi_id', $user->id))->orderBy('nama')->get();
        $tahunOptions = RpsReview::query()->distinct()->orderByDesc('tahun_akademik')->pluck('tahun_akademik');

        $stats = [
            'total' => (clone $query)->count(),
            'sesuai' => (clone $query)->where('status', 'sesuai')->count(),
            'perlu_revisi' => (clone $query)->where('status', 'perlu_revisi')->count(),
            'belum' => (clone $query)->where('status', 'belum_direview')->count(),
        ];

        return view('admin.ami.rps-review.index', compact('rps', 'prodis', 'tahunOptions', 'stats'));
    }

    public function create()
    {
        $prodis = $this->prodiOptions();

        return view('admin.ami.rps-review.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $this->assertProdiAllowed($validated['prodi_id']);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $validated['file_path'] = $file->store('rps-review', 'public');
            $validated['file_name'] = $file->getClientOriginalName();
        }

        $validated['submitted_by'] = auth()->id();

        RpsReview::create($validated);

        return redirect()->route('admin.ami.rps-review.index')->with('success', 'Data RPS berhasil ditambahkan.');
    }

    public function edit(RpsReview $rpsReview)
    {
        $this->assertProdiAllowed($rpsReview->prodi_id);
        $prodis = $this->prodiOptions();

        return view('admin.ami.rps-review.edit', compact('rpsReview', 'prodis'));
    }

    public function update(Request $request, RpsReview $rpsReview)
    {
        $this->assertProdiAllowed($rpsReview->prodi_id);

        $validated = $this->validated($request);
        $this->assertProdiAllowed($validated['prodi_id']);

        if ($request->hasFile('file')) {
            if ($rpsReview->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($rpsReview->file_path);
            }
            $file = $request->file('file');
            $validated['file_path'] = $file->store('rps-review', 'public');
            $validated['file_name'] = $file->getClientOriginalName();

            // A replaced file needs to be reviewed again.
            if ($rpsReview->status !== 'belum_direview') {
                $validated['status'] = 'belum_direview';
                $validated['catatan_reviewer'] = null;
                $validated['reviewed_by'] = null;
                $validated['reviewed_at'] = null;
            }
        }

        $rpsReview->update($validated);

        return redirect()->route('admin.ami.rps-review.index')->with('success', 'Data RPS berhasil diperbarui.');
    }

    public function destroy(RpsReview $rpsReview)
    {
        $this->assertProdiAllowed($rpsReview->prodi_id);

        $rpsReview->delete();

        return redirect()->route('admin.ami.rps-review.index')->with('success', 'Data RPS dihapus.');
    }

    /**
     * Reviewer sets the review outcome (sesuai / perlu revisi).
     */
    public function review(Request $request, RpsReview $rpsReview)
    {
        $this->assertProdiAllowed($rpsReview->prodi_id);

        $data = $request->validate([
            'status' => 'required|in:sesuai,perlu_revisi,belum_direview',
            'catatan_reviewer' => 'nullable|string|max:2000',
        ]);

        if ($data['status'] === 'perlu_revisi' && blank($data['catatan_reviewer'] ?? null)) {
            return back()->with('error', 'Catatan wajib diisi saat meminta revisi.');
        }

        $rpsReview->update([
            'status' => $data['status'],
            'catatan_reviewer' => $data['catatan_reviewer'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Status review RPS diperbarui.');
    }

    // ---------------------------------------------------------------------

    private function validated(Request $request): array
    {
        return $request->validate([
            'prodi_id' => 'required|exists:prodi,id',
            'kode_mk' => 'nullable|string|max:50',
            'mata_kuliah' => 'required|string|max:255',
            'dosen_pengampu' => 'required|string|max:255',
            'sks' => 'nullable|integer|min:1|max:12',
            'semester' => 'required|in:ganjil,genap',
            'tahun_akademik' => 'required|string|max:20',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
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
                'Anda hanya dapat mengelola RPS program studi Anda sendiri.'
            );
        }
    }
}
