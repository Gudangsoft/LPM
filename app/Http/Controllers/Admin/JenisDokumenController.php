<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JenisDokumenController extends Controller
{
    public function index()
    {
        $jenisDokumen = JenisDokumen::withCount('dokumen')->orderBy('urutan')->paginate(15);
        return view('admin.jenis-dokumen.index', compact('jenisDokumen'));
    }

    public function create()
    {
        return view('admin.jenis-dokumen.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'urutan' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        $validated['is_active'] = $request->has('is_active');
        $validated['urutan'] = $validated['urutan'] ?? 0;
        
        $count = 1;
        $originalSlug = $validated['slug'];
        while (JenisDokumen::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        JenisDokumen::create($validated);

        return redirect()->route('admin.jenis-dokumen.index')
            ->with('success', __('admin.document_type_created'));
    }

    public function edit(JenisDokumen $jenisDokuman)
    {
        return view('admin.jenis-dokumen.edit', ['jenisDokumen' => $jenisDokuman]);
    }

    public function update(Request $request, JenisDokumen $jenisDokuman)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'urutan' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($jenisDokuman->nama !== $validated['nama']) {
            $validated['slug'] = Str::slug($validated['nama']);
            $count = 1;
            $originalSlug = $validated['slug'];
            while (JenisDokumen::where('slug', $validated['slug'])->where('id', '!=', $jenisDokuman->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        $jenisDokuman->update($validated);

        return redirect()->route('admin.jenis-dokumen.index')
            ->with('success', __('admin.document_type_updated'));
    }

    public function destroy(JenisDokumen $jenisDokuman)
    {
        if ($jenisDokuman->dokumen()->count() > 0) {
            return redirect()->route('admin.jenis-dokumen.index')
                ->with('error', __('admin.document_type_has_documents'));
        }

        $jenisDokuman->delete();

        return redirect()->route('admin.jenis-dokumen.index')
            ->with('success', __('admin.document_type_deleted'));
    }
}
