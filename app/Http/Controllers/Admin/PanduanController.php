<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Panduan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PanduanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:panduan.view', only: ['index', 'exportPdf']),
            new Middleware('permission:panduan.manage', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $chapters = Panduan::active()->ordered()->get()->groupBy('kategori');

        return view('admin.panduan.index', compact('chapters'));
    }

    public function create()
    {
        $kategoris = Panduan::active()->orderBy('kategori')->pluck('kategori')->unique()->values();

        return view('admin.panduan.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|max:100',
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'icon' => 'nullable|string|max:50',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['urutan'] = $validated['urutan'] ?? (Panduan::max('urutan') + 1);

        Panduan::create($validated);

        return redirect()->route('admin.panduan.index')
            ->with('success', 'Bab panduan berhasil ditambahkan.');
    }

    public function edit(Panduan $panduan)
    {
        $kategoris = Panduan::active()->orderBy('kategori')->pluck('kategori')->unique()->values();

        return view('admin.panduan.edit', compact('panduan', 'kategoris'));
    }

    public function update(Request $request, Panduan $panduan)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|max:100',
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'icon' => 'nullable|string|max:50',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $panduan->update($validated);

        return redirect()->route('admin.panduan.index')
            ->with('success', 'Bab panduan berhasil diperbarui.');
    }

    public function destroy(Panduan $panduan)
    {
        $panduan->delete();

        return redirect()->route('admin.panduan.index')
            ->with('success', 'Bab panduan berhasil dihapus.');
    }

    public function exportPdf()
    {
        $chapters = Panduan::active()->ordered()->get()->groupBy('kategori');

        $pdf = Pdf::loadView('admin.panduan.pdf', compact('chapters'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('buku-panduan-lpm.pdf');
    }
}
