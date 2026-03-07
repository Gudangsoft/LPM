<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::ordered()->paginate(15);
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link' => 'nullable|url|max:255',
            'button_text' => 'nullable|string|max:50',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['gambar'] = $request->file('gambar')->store('sliders', 'public');

        Slider::create($validated);

        return redirect()->route('admin.sliders.index')
            ->with('success', __('admin.slider_created'));
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link' => 'nullable|url|max:255',
            'button_text' => 'nullable|string|max:50',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('gambar')) {
            Storage::disk('public')->delete($slider->gambar);
            $validated['gambar'] = $request->file('gambar')->store('sliders', 'public');
        }

        $slider->update($validated);

        return redirect()->route('admin.sliders.index')
            ->with('success', __('admin.slider_updated'));
    }

    public function destroy(Slider $slider)
    {
        Storage::disk('public')->delete($slider->gambar);
        $slider->delete();

        return redirect()->route('admin.sliders.index')
            ->with('success', __('admin.slider_deleted'));
    }
}
