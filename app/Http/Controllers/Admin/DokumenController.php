<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\DokumenVersion;
use App\Models\JenisDokumen;
use App\Models\StandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DokumenController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:dokumen.view', only: ['index', 'show']),
            new Middleware('permission:dokumen.upload', only: ['create', 'store']),
            new Middleware('permission:dokumen.manage', only: ['edit', 'update', 'destroy', 'submit', 'approve', 'reject']),
        ];
    }

    /**
     * Users without dokumen.manage (Asesor, Dosen) only see documents that are
     * publicly approved, plus their own uploads regardless of status so they
     * can track review progress.
     */
    private function applyVisibilityScope($query)
    {
        if (!auth()->user()->hasPermission('dokumen.manage')) {
            $query->where(function ($q) {
                $q->where(fn ($qq) => $qq->where('status', 'approved')->where('is_active', true))
                  ->orWhere('uploaded_by', auth()->id());
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->applyVisibilityScope(Dokumen::with('jenisDokumen')->latest());

        if ($request->has('search') && $request->search) {
            $query->where('judul', 'like', "%{$request->search}%");
        }

        if ($request->has('jenis') && $request->jenis) {
            $query->where('jenis_dokumen_id', $request->jenis);
        }

        $dokumens = $query->paginate(20);
        $jenisDokumen = JenisDokumen::active()->ordered()->get();

        return view('admin.dokumen.index', compact('dokumens', 'jenisDokumen'));
    }

    public function create()
    {
        $jenisDokumen = JenisDokumen::active()->ordered()->get();
        $standarMutus = StandarMutu::active()->ordered()->get();
        return view('admin.dokumen.create', compact('jenisDokumen', 'standarMutus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'jenis_dokumen_id' => 'nullable|exists:jenis_dokumen,id',
            'standar_mutu_id' => 'nullable|exists:standar_mutu,id',
            'kategori' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['slug'] = Str::slug($validated['judul']);
        
        $count = 1;
        $originalSlug = $validated['slug'];
        while (Dokumen::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        $file = $request->file('file');
        $validated['file_path'] = $file->store('dokumen', 'public');
        $validated['file_name'] = $file->getClientOriginalName();
        $validated['file_size'] = $file->getSize();
        $validated['file_type'] = $file->getClientOriginalExtension();
        unset($validated['file']);

        $validated['uploaded_by'] = auth()->id();
        $validated['status'] = 'draft';

        Dokumen::create($validated);

        return redirect()->route('admin.dokumen.index')
            ->with('success', __('admin.document_created'));
    }

    public function show(Dokumen $dokumen)
    {
        $canSeeAny = auth()->user()->hasPermission('dokumen.manage');
        $isOwnUpload = $dokumen->uploaded_by === auth()->id();
        $isPubliclyApproved = $dokumen->status === 'approved' && $dokumen->is_active;

        if (!$canSeeAny && !$isOwnUpload && !$isPubliclyApproved) {
            abort(404);
        }

        $dokumen->load(['jenisDokumen', 'standarMutu', 'uploader', 'reviewer', 'versions.uploader']);

        return view('admin.dokumen.show', compact('dokumen'));
    }

    public function edit(Dokumen $dokumen)
    {
        $jenisDokumen = JenisDokumen::active()->ordered()->get();
        $standarMutus = StandarMutu::active()->ordered()->get();
        return view('admin.dokumen.edit', compact('dokumen', 'jenisDokumen', 'standarMutus'));
    }

    public function update(Request $request, Dokumen $dokumen)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'jenis_dokumen_id' => 'nullable|exists:jenis_dokumen,id',
            'standar_mutu_id' => 'nullable|exists:standar_mutu,id',
            'kategori' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($dokumen->judul !== $validated['judul']) {
            $validated['slug'] = Str::slug($validated['judul']);
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Dokumen::where('slug', $validated['slug'])->where('id', '!=', $dokumen->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        if ($request->hasFile('file')) {
            // Snapshot the file being replaced into version history before
            // overwriting it, instead of deleting it outright.
            DokumenVersion::create([
                'dokumen_id' => $dokumen->id,
                'file_path' => $dokumen->file_path,
                'file_name' => $dokumen->file_name,
                'file_size' => $dokumen->file_size,
                'file_type' => $dokumen->file_type,
                'version_number' => $dokumen->current_version,
                'uploaded_by' => $dokumen->uploaded_by,
            ]);

            $file = $request->file('file');
            $validated['file_path'] = $file->store('dokumen', 'public');
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['current_version'] = $dokumen->current_version + 1;
            $validated['uploaded_by'] = auth()->id();

            // A changed file needs to be reviewed again.
            if ($dokumen->status === 'approved') {
                $validated['status'] = 'draft';
            }
        }
        unset($validated['file']);

        $dokumen->update($validated);

        return redirect()->route('admin.dokumen.index')
            ->with('success', __('admin.document_updated'));
    }

    public function destroy(Dokumen $dokumen)
    {
        Storage::disk('public')->delete($dokumen->file_path);
        Storage::disk('public')->delete($dokumen->versions->pluck('file_path')->all());
        $dokumen->delete();

        return redirect()->route('admin.dokumen.index')
            ->with('success', __('admin.document_deleted'));
    }

    public function submit(Dokumen $dokumen)
    {
        $dokumen->submit();

        return back()->with('success', 'Dokumen berhasil diajukan untuk direview.');
    }

    public function approve(Request $request, Dokumen $dokumen)
    {
        $dokumen->approve(auth()->user(), $request->input('catatan_reviewer'));

        return back()->with('success', 'Dokumen berhasil disetujui.');
    }

    public function reject(Request $request, Dokumen $dokumen)
    {
        $validated = $request->validate([
            'catatan_reviewer' => 'required|string',
        ]);

        $dokumen->reject(auth()->user(), $validated['catatan_reviewer']);

        return back()->with('success', 'Dokumen ditolak.');
    }
}
