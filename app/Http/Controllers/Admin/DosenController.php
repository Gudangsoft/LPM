<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class DosenController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:dosen.view', only: ['index']),
            new Middleware('permission:dosen.manage', only: ['create', 'store', 'edit', 'update', 'destroy', 'bulkDestroy']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Dosen::with(['prodi', 'user.roles'])
            ->visibleTo($user)
            ->when($request->filled('prodi_id'), fn ($q) => $q->where('prodi_id', $request->prodi_id))
            ->when($request->filled('jabatan'), fn ($q) => $q->where('jabatan_akademik', $request->jabatan))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'aktif'))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($qq) use ($request) {
                $qq->where('nama', 'like', "%{$request->search}%")
                    ->orWhere('nidn_nidk', 'like', "%{$request->search}%")
                    ->orWhere('kode_dosen', 'like', "%{$request->search}%")
                    ->orWhere('bidang_keahlian', 'like', "%{$request->search}%");
            }))
            ->orderBy('nama');

        $dosen = $query->paginate(20)->withQueryString();
        $prodis = $this->prodiOptions();
        $roles = $user->isAdmin() ? \App\Models\Role::orderBy('name')->get() : collect();
        $tanpaAkun = $user->isAdmin()
            ? Dosen::whereNull('user_id')->when($request->filled('prodi_id'), fn ($q) => $q->where('prodi_id', $request->prodi_id))->count()
            : 0;

        return view('admin.dosen.index', compact('dosen', 'prodis', 'roles', 'tanpaAkun'));
    }

    public function create()
    {
        $prodis = $this->prodiOptions();

        return view('admin.dosen.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $this->assertProdiAllowed($validated['prodi_id']);

        Dosen::create($validated);

        return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function edit(Dosen $dosen)
    {
        $this->assertProdiAllowed($dosen->prodi_id);
        $prodis = $this->prodiOptions();

        return view('admin.dosen.edit', compact('dosen', 'prodis'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $this->assertProdiAllowed($dosen->prodi_id);
        $validated = $this->validated($request, $dosen);
        $this->assertProdiAllowed($validated['prodi_id']);

        $dosen->update($validated);

        return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function destroy(Dosen $dosen)
    {
        $this->assertProdiAllowed($dosen->prodi_id);
        $dosen->delete();

        return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil dihapus.');
    }

    /**
     * Hapus massal: yang dicentang, atau (admin) semua hasil filter saat ini.
     * Akun login dosen tidak ikut terhapus; kaprodi hanya bisa menghapus dosen prodinya.
     */
    public function bulkDestroy(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'scope' => 'required|in:selected,filter',
            'dosen_ids' => 'required_if:scope,selected|array',
            'dosen_ids.*' => 'integer',
        ], [
            'dosen_ids.required_if' => 'Centang dulu dosen yang akan dihapus.',
        ]);

        $query = Dosen::query()->visibleTo($user);
        if ($validated['scope'] === 'selected') {
            $query->whereIn('id', $validated['dosen_ids']);
        } else {
            abort_unless($user->isAdmin(), 403);
            $query->when($request->filled('prodi_id'), fn ($q) => $q->where('prodi_id', $request->prodi_id))
                ->when($request->filled('jabatan'), fn ($q) => $q->where('jabatan_akademik', $request->jabatan))
                ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'aktif'))
                ->when($request->filled('search'), fn ($q) => $q->where(function ($qq) use ($request) {
                    $qq->where('nama', 'like', "%{$request->search}%")
                        ->orWhere('nidn_nidk', 'like', "%{$request->search}%")
                        ->orWhere('kode_dosen', 'like', "%{$request->search}%")
                        ->orWhere('bidang_keahlian', 'like', "%{$request->search}%");
                }));
        }

        $jumlah = 0;
        $query->select('id')->chunkById(500, function ($rows) use (&$jumlah) {
            $jumlah += Dosen::whereIn('id', $rows->pluck('id'))->delete();
        });

        return redirect()->route('admin.dosen.index', $request->only('prodi_id', 'jabatan', 'status', 'search'))
            ->with($jumlah ? 'success' : 'error', $jumlah ? "{$jumlah} data dosen berhasil dihapus." : 'Tidak ada data dosen yang dihapus.');
    }

    // ---------------------------------------------------------------------

    private function validated(Request $request, ?Dosen $dosen = null): array
    {
        $validated = $request->validate([
            'prodi_id' => 'required|exists:prodi,id',
            'kode_dosen' => 'nullable|string|max:30',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => ['nullable', Rule::in(array_keys(Dosen::JENIS_KELAMIN))],
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date|before:today',
            'agama' => 'nullable|string|max:30',
            'nip' => 'nullable|string|max:30',
            'nidn_nidk' => ['nullable', 'string', 'max:50', Rule::unique('dosen', 'nidn_nidk')->ignore($dosen?->id)],
            'nuptk' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:1000',
            'pendidikan_terakhir' => ['nullable', Rule::in(Dosen::PENDIDIKAN)],
            'lulusan' => 'nullable|string|max:255',
            'pendidikan_magister_bidang' => 'nullable|string|max:255',
            'pendidikan_doktor_bidang' => 'nullable|string|max:255',
            'bidang_keahlian' => 'nullable|string|max:255',
            'golongan' => 'nullable|string|max:30',
            'jabatan_akademik' => ['nullable', Rule::in(array_keys(Dosen::JABATAN))],
            'no_sertifikat_pendidik' => 'nullable|string|max:100',
        ], [
            'nidn_nidk.unique' => 'NIDN/NIDK ini sudah terdaftar untuk dosen lain.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    private function prodiOptions()
    {
        $user = auth()->user();

        return Prodi::when(! $user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->where('kaprodi_id', $user->id))
            ->orderBy('nama')->get();
    }

    private function assertProdiAllowed(int $prodiId): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isKaprodi()) {
            abort_unless(
                Prodi::where('id', $prodiId)->where('kaprodi_id', $user->id)->exists(),
                403,
                'Anda hanya dapat mengelola dosen program studi Anda sendiri.'
            );
        }
    }
}
