<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Halaman;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HalamanController extends Controller
{
    public function index()
    {
        $halamans = Halaman::latest()->paginate(15);
        return view('admin.halaman.index', compact('halamans'));
    }

    public function create()
    {
        return view('admin.halaman.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'konten_en' => 'nullable|string',
            'template' => 'nullable|string|max:50',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['slug'] = Str::slug($validated['judul']);
        
        $count = 1;
        $originalSlug = $validated['slug'];
        while (Halaman::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        // Handle translations
        if ($request->konten_en) {
            $validated['konten_translations'] = [
                'en' => $request->konten_en
            ];
        }
        unset($validated['konten_en']);

        Halaman::create($validated);

        return redirect()->route('admin.halaman.index')
            ->with('success', __('admin.page_created'));
    }

    public function edit(Halaman $halaman)
    {
        return view('admin.halaman.edit', compact('halaman'));
    }

    public function update(Request $request, Halaman $halaman)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'konten_en' => 'nullable|string',
            'template' => 'nullable|string|max:50',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($halaman->judul !== $validated['judul']) {
            $validated['slug'] = Str::slug($validated['judul']);
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Halaman::where('slug', $validated['slug'])->where('id', '!=', $halaman->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        // Handle translations
        $translations = $halaman->konten_translations ?: [];
        if ($request->konten_en) {
            $translations['en'] = $request->konten_en;
        }
        $validated['konten_translations'] = $translations;
        unset($validated['konten_en']);

        $halaman->update($validated);

        return redirect()->route('admin.halaman.index')
            ->with('success', __('admin.page_updated'));
    }

    public function destroy(Halaman $halaman)
    {
        $halaman->delete();

        return redirect()->route('admin.halaman.index')
            ->with('success', __('admin.page_deleted'));
    }
}
