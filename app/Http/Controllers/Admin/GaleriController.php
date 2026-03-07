<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        $query = Galeri::latest();
        
        if ($request->has('search') && $request->search) {
            $query->where('judul', 'like', "%{$request->search}%");
        }
        
        $galeris = $query->paginate(20);
        
        return view('admin.galeri.index', compact('galeris'));
    }

    public function create()
    {
        return view('admin.galeri.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'kategori' => 'nullable|string|max:100',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['slug'] = Str::slug($validated['judul']);
        
        $count = 1;
        $originalSlug = $validated['slug'];
        while (Galeri::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        $validated['gambar'] = $request->file('gambar')->store('galeri', 'public');

        Galeri::create($validated);

        return redirect()->route('admin.galeri.index')
            ->with('success', __('admin.gallery_created'));
    }

    public function edit(Galeri $galeri)
    {
        return view('admin.galeri.edit', compact('galeri'));
    }

    public function update(Request $request, Galeri $galeri)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'kategori' => 'nullable|string|max:100',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($galeri->judul !== $validated['judul']) {
            $validated['slug'] = Str::slug($validated['judul']);
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Galeri::where('slug', $validated['slug'])->where('id', '!=', $galeri->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        if ($request->hasFile('gambar')) {
            Storage::disk('public')->delete($galeri->gambar);
            $validated['gambar'] = $request->file('gambar')->store('galeri', 'public');
        }

        $galeri->update($validated);

        return redirect()->route('admin.galeri.index')
            ->with('success', __('admin.gallery_updated'));
    }

    public function destroy(Galeri $galeri)
    {
        Storage::disk('public')->delete($galeri->gambar);
        $galeri->delete();

        return redirect()->route('admin.galeri.index')
            ->with('success', __('admin.gallery_deleted'));
    }
}
