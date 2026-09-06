<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StandarMutu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StandarMutuController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:standar-mutu.view', only: ['index']),
            new Middleware('permission:standar-mutu.manage', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $standarMutus = StandarMutu::withCount(['temuan', 'dokumen'])->ordered()->paginate(15);

        return view('admin.standar-mutu.index', compact('standarMutus'));
    }

    public function create()
    {
        return view('admin.standar-mutu.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'nullable|string|max:20',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['urutan'] = $validated['urutan'] ?? 0;

        StandarMutu::create($validated);

        return redirect()->route('admin.standar-mutu.index')
            ->with('success', 'Standar mutu berhasil ditambahkan.');
    }

    public function edit(StandarMutu $standarMutu)
    {
        return view('admin.standar-mutu.edit', compact('standarMutu'));
    }

    public function update(Request $request, StandarMutu $standarMutu)
    {
        $validated = $request->validate([
            'kode' => 'nullable|string|max:20',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $standarMutu->update($validated);

        return redirect()->route('admin.standar-mutu.index')
            ->with('success', 'Standar mutu berhasil diperbarui.');
    }

    public function destroy(StandarMutu $standarMutu)
    {
        if ($standarMutu->temuan()->count() > 0 || $standarMutu->dokumen()->count() > 0) {
            return redirect()->route('admin.standar-mutu.index')
                ->with('error', 'Standar mutu tidak dapat dihapus karena masih digunakan oleh temuan atau dokumen.');
        }

        $standarMutu->delete();

        return redirect()->route('admin.standar-mutu.index')
            ->with('success', 'Standar mutu berhasil dihapus.');
    }
}
