<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dokumen::with('jenisDokumen')->latest();
        
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
        return view('admin.dokumen.create', compact('jenisDokumen'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'jenis_dokumen_id' => 'nullable|exists:jenis_dokumen,id',
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

        Dokumen::create($validated);

        return redirect()->route('admin.dokumen.index')
            ->with('success', __('admin.document_created'));
    }

    public function edit(Dokumen $dokumen)
    {
        $jenisDokumen = JenisDokumen::active()->ordered()->get();
        return view('admin.dokumen.edit', compact('dokumen', 'jenisDokumen'));
    }

    public function update(Request $request, Dokumen $dokumen)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'jenis_dokumen_id' => 'nullable|exists:jenis_dokumen,id',
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
            Storage::disk('public')->delete($dokumen->file_path);
            $file = $request->file('file');
            $validated['file_path'] = $file->store('dokumen', 'public');
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getClientOriginalExtension();
        }
        unset($validated['file']);

        $dokumen->update($validated);

        return redirect()->route('admin.dokumen.index')
            ->with('success', __('admin.document_updated'));
    }

    public function destroy(Dokumen $dokumen)
    {
        Storage::disk('public')->delete($dokumen->file_path);
        $dokumen->delete();

        return redirect()->route('admin.dokumen.index')
            ->with('success', __('admin.document_deleted'));
    }
}
