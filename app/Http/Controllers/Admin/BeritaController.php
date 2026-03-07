<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\KategoriBerita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::with(['kategori', 'user'])->latest();
        
        if ($request->has('search') && $request->search) {
            $query->where('judul', 'like', "%{$request->search}%");
        }
        
        $berita = $query->paginate(15);
        
        return view('admin.berita.index', compact('berita'));
    }

    public function create()
    {
        $kategoris = KategoriBerita::active()->get();
        return view('admin.berita.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_berita,id',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['judul']);
        
        // Ensure unique slug
        $count = 1;
        $originalSlug = $validated['slug'];
        while (Berita::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('berita', 'public');
        }

        if ($request->is_published) {
            $validated['published_at'] = now();
        }

        Berita::create($validated);

        return redirect()->route('admin.berita.index')
            ->with('success', __('admin.news_created'));
    }

    public function edit(Berita $berita)
    {
        $kategoris = KategoriBerita::active()->get();
        return view('admin.berita.edit', compact('berita', 'kategoris'));
    }

    public function update(Request $request, Berita $berita)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_berita,id',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        if ($berita->judul !== $validated['judul']) {
            $validated['slug'] = Str::slug($validated['judul']);
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Berita::where('slug', $validated['slug'])->where('id', '!=', $berita->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        if ($request->hasFile('thumbnail')) {
            if ($berita->thumbnail) {
                Storage::disk('public')->delete($berita->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('berita', 'public');
        }

        if ($request->is_published && !$berita->published_at) {
            $validated['published_at'] = now();
        }

        $berita->update($validated);

        return redirect()->route('admin.berita.index')
            ->with('success', __('admin.news_updated'));
    }

    public function destroy(Berita $berita)
    {
        if ($berita->thumbnail) {
            Storage::disk('public')->delete($berita->thumbnail);
        }
        
        $berita->delete();

        return redirect()->route('admin.berita.index')
            ->with('success', __('admin.news_deleted'));
    }
}
