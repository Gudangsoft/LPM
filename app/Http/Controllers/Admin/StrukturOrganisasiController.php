<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StrukturOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StrukturOrganisasiController extends Controller
{
    public function index()
    {
        $strukturs = StrukturOrganisasi::ordered()->paginate(20);
        return view('admin.struktur-organisasi.index', compact('strukturs'));
    }

    public function create()
    {
        return view('admin.struktur-organisasi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'bio' => 'nullable|string|max:2000',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('struktur-organisasi', 'public');
        }

        StrukturOrganisasi::create($validated);

        return redirect()->route('admin.struktur-organisasi.index')
            ->with('success', __('admin.structure_created'));
    }

    public function edit(StrukturOrganisasi $struktur)
    {
        return view('admin.struktur-organisasi.edit', compact('struktur'));
    }

    public function update(Request $request, StrukturOrganisasi $struktur)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'bio' => 'nullable|string|max:2000',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('foto')) {
            if ($struktur->foto) {
                Storage::disk('public')->delete($struktur->foto);
            }
            $validated['foto'] = $request->file('foto')->store('struktur-organisasi', 'public');
        }

        $struktur->update($validated);

        return redirect()->route('admin.struktur-organisasi.index')
            ->with('success', __('admin.structure_updated'));
    }

    public function destroy(StrukturOrganisasi $struktur)
    {
        if ($struktur->foto) {
            Storage::disk('public')->delete($struktur->foto);
        }
        
        $struktur->delete();

        return redirect()->route('admin.struktur-organisasi.index')
            ->with('success', __('admin.structure_deleted'));
    }
}
