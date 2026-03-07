<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriBerita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriBeritaController extends Controller
{
    public function index()
    {
        $kategoris = KategoriBerita::withCount('berita')->latest()->paginate(15);
        return view('admin.kategori-berita.index', compact('kategoris'));
    }

    public function create()
    {
        return view('admin.kategori-berita.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        
        $count = 1;
        $originalSlug = $validated['slug'];
        while (KategoriBerita::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        KategoriBerita::create($validated);

        return redirect()->route('admin.kategori-berita.index')
            ->with('success', __('admin.category_created'));
    }

    public function edit(KategoriBerita $kategori)
    {
        return view('admin.kategori-berita.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriBerita $kategori)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        if ($kategori->nama !== $validated['nama']) {
            $validated['slug'] = Str::slug($validated['nama']);
            $count = 1;
            $originalSlug = $validated['slug'];
            while (KategoriBerita::where('slug', $validated['slug'])->where('id', '!=', $kategori->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        $kategori->update($validated);

        return redirect()->route('admin.kategori-berita.index')
            ->with('success', __('admin.category_updated'));
    }

    public function destroy(KategoriBerita $kategori)
    {
        if ($kategori->berita()->count() > 0) {
            return redirect()->route('admin.kategori-berita.index')
                ->with('error', __('admin.category_has_news'));
        }

        $kategori->delete();

        return redirect()->route('admin.kategori-berita.index')
            ->with('success', __('admin.category_deleted'));
    }
}
