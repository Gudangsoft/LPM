<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        $query = Prodi::with(['kaprodi', 'latestAkreditasi'])->latest();
        
        if ($request->has('search') && $request->search) {
            $query->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('kode', 'like', "%{$request->search}%");
        }
        
        if ($request->has('jenjang') && $request->jenjang) {
            $query->where('jenjang', $request->jenjang);
        }
        
        $prodi = $query->paginate(15);
        
        return view('admin.prodi.index', compact('prodi'));
    }

    public function create()
    {
        $users = User::active()->get();
        $jenjangOptions = ['D3', 'D4', 'S1', 'S2', 'S3'];
        
        return view('admin.prodi.create', compact('users', 'jenjangOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:prodi,kode',
            'nama' => 'required|string|max:255',
            'jenjang' => 'required|string|in:D3,D4,S1,S2,S3',
            'fakultas' => 'nullable|string|max:255',
            'kaprodi_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Prodi::create($validated);

        return redirect()->route('admin.prodi.index')
            ->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function show(Prodi $prodi)
    {
        $prodi->load(['kaprodi', 'akreditasi', 'jadwalAmi.periodeAmi']);
        
        return view('admin.prodi.show', compact('prodi'));
    }

    public function edit(Prodi $prodi)
    {
        $users = User::active()->get();
        $jenjangOptions = ['D3', 'D4', 'S1', 'S2', 'S3'];
        
        return view('admin.prodi.edit', compact('prodi', 'users', 'jenjangOptions'));
    }

    public function update(Request $request, Prodi $prodi)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:prodi,kode,' . $prodi->id,
            'nama' => 'required|string|max:255',
            'jenjang' => 'required|string|in:D3,D4,S1,S2,S3',
            'fakultas' => 'nullable|string|max:255',
            'kaprodi_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $prodi->update($validated);

        return redirect()->route('admin.prodi.index')
            ->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(Prodi $prodi)
    {
        $prodi->delete();

        return redirect()->route('admin.prodi.index')
            ->with('success', 'Program Studi berhasil dihapus.');
    }
}
