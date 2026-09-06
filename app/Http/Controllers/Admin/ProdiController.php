<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProdiController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:prodi.view', only: ['index', 'show']),
            new Middleware('permission:prodi.create', only: ['create', 'store']),
            new Middleware('permission:prodi.edit', only: ['edit', 'update']),
            new Middleware('permission:prodi.delete', only: ['destroy']),
        ];
    }

    /**
     * Assign the kaprodi Role to $userId, or remove it from $userId if they no
     * longer head any prodi. Keeps the Role pivot in sync with kaprodi_id.
     */
    private function syncKaprodiRole(?int $newUserId, ?int $oldUserId = null): void
    {
        $kaprodiRole = Role::where('slug', 'kaprodi')->first();
        if (!$kaprodiRole) {
            return;
        }

        if ($newUserId && (!$oldUserId || $newUserId !== $oldUserId)) {
            if ($user = User::find($newUserId)) {
                $user->assignRole($kaprodiRole);
            }
        }

        if ($oldUserId && $oldUserId !== $newUserId && Prodi::where('kaprodi_id', $oldUserId)->count() === 0) {
            if ($user = User::find($oldUserId)) {
                $user->removeRole($kaprodiRole);
            }
        }
    }

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

        if (!empty($validated['kaprodi_id'])) {
            $this->syncKaprodiRole($validated['kaprodi_id']);
        }

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

        $oldKaprodiId = $prodi->kaprodi_id;
        $prodi->update($validated);
        $this->syncKaprodiRole($validated['kaprodi_id'] ?? null, $oldKaprodiId);

        return redirect()->route('admin.prodi.index')
            ->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(Prodi $prodi)
    {
        $oldKaprodiId = $prodi->kaprodi_id;
        $prodi->delete();
        $this->syncKaprodiRole(null, $oldKaprodiId);

        return redirect()->route('admin.prodi.index')
            ->with('success', 'Program Studi berhasil dihapus.');
    }
}
