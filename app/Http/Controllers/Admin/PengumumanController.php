<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengumuman::latest();
        
        if ($request->has('search') && $request->search) {
            $query->where('judul', 'like', "%{$request->search}%");
        }
        
        $pengumumans = $query->paginate(15);
        
        return view('admin.pengumuman.index', compact('pengumumans'));
    }

    public function create()
    {
        return view('admin.pengumuman.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'is_important' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $validated['is_important'] = $request->boolean('is_important');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['slug'] = Str::slug($validated['judul']);
        
        $count = 1;
        $originalSlug = $validated['slug'];
        while (Pengumuman::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        if ($request->hasFile('lampiran')) {
            $validated['lampiran'] = $request->file('lampiran')->store('pengumuman', 'public');
        }

        Pengumuman::create($validated);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', __('admin.announcement_created'));
    }

    public function edit(Pengumuman $pengumuman)
    {
        return view('admin.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'is_important' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $validated['is_important'] = $request->boolean('is_important');
        $validated['is_active'] = $request->boolean('is_active');

        if ($pengumuman->judul !== $validated['judul']) {
            $validated['slug'] = Str::slug($validated['judul']);
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Pengumuman::where('slug', $validated['slug'])->where('id', '!=', $pengumuman->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        if ($request->hasFile('lampiran')) {
            if ($pengumuman->lampiran) {
                Storage::disk('public')->delete($pengumuman->lampiran);
            }
            $validated['lampiran'] = $request->file('lampiran')->store('pengumuman', 'public');
        }

        $pengumuman->update($validated);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', __('admin.announcement_updated'));
    }

    public function destroy(Pengumuman $pengumuman)
    {
        if ($pengumuman->lampiran) {
            Storage::disk('public')->delete($pengumuman->lampiran);
        }
        
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')
            ->with('success', __('admin.announcement_deleted'));
    }
}
