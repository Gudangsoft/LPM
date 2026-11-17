<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Buatkan akun login untuk dosen dari menu Data Dosen (khusus admin).
 *
 * - Login memakai email dosen; dosen tanpa email dilewati.
 * - Bila email sudah dipakai akun lain, dosen ditautkan ke akun itu (tidak dibuat ganda).
 * - Password awal: NIDN/NIDK (cadangan: Kode Dosen) atau satu password yang ditentukan admin.
 * - Role opsional; bila dikosongkan, role diatur kemudian di menu Pengguna.
 */
class DosenAkunController extends Controller
{
    /** Jumlah dosen maksimal per klik: mode NIDN hash tiap akun (~0,2 dtk), mode password tunggal hash sekali. */
    private const BATCH_NIDN = 50;

    private const BATCH_CUSTOM = 500;

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya admin yang dapat membuat akun pengguna.');

        $validated = $request->validate([
            'scope' => 'required|in:selected,all',
            'dosen_ids' => 'required_if:scope,selected|array',
            'dosen_ids.*' => 'integer',
            'prodi_id' => 'nullable|integer',
            'password_mode' => 'required|in:nidn,custom',
            'password' => 'required_if:password_mode,custom|nullable|string|min:8|max:100',
            'role_id' => ['nullable', Rule::exists('roles', 'id')],
        ], [
            'dosen_ids.required_if' => 'Centang dulu dosen yang akan dibuatkan akun.',
            'password.required_if' => 'Isi password awal.',
            'password.min' => 'Password awal minimal 8 karakter.',
        ]);

        $query = Dosen::with('user')->whereNull('user_id');
        if ($validated['scope'] === 'selected') {
            $query->whereIn('id', $validated['dosen_ids'] ?? []);
        } elseif (! empty($validated['prodi_id'])) {
            $query->where('prodi_id', $validated['prodi_id']);
        }
        // Hash password (bcrypt) ~0,2 detik per akun. Batasi per klik agar tidak melewati
        // max_execution_time server (umumnya 30 detik); sisanya diproses pada klik berikutnya.
        @set_time_limit(300);

        // Dosen tanpa email tidak bisa login -> keluarkan dari antrean (kalau tidak, mereka
        // akan selalu mengisi jatah batch pertama), cukup dilaporkan.
        $tanpaEmail = (clone $query)->where(fn ($q) => $q->whereNull('email')->orWhere('email', ''))->orderBy('nama')->pluck('nama');
        $query->whereNotNull('email')->where('email', '!=', '');

        $totalPending = (clone $query)->count();
        $dosenList = $query->orderBy('nama')->limit($validated['password_mode'] === 'custom' ? self::BATCH_CUSTOM : self::BATCH_NIDN)->get();

        $skipped = $tanpaEmail->take(50)->map(fn ($n) => "{$n}: belum punya email")->all();
        if ($tanpaEmail->count() > 50) {
            $skipped[] = '... dan '.($tanpaEmail->count() - 50).' dosen lain tanpa email';
        }

        if ($dosenList->isEmpty()) {
            return back()
                ->with('error', $tanpaEmail->isNotEmpty()
                    ? 'Tidak ada akun yang dibuat: dosen yang tersisa belum punya email.'
                    : 'Tidak ada dosen yang perlu dibuatkan akun (semua yang dipilih sudah punya akun).')
                ->with('akun_skipped', $skipped);
        }

        $role = ! empty($validated['role_id']) ? Role::find($validated['role_id']) : null;
        $created = 0;
        $linked = 0;

        $hashCache = []; // mode "satu password": cukup di-hash sekali

        DB::transaction(function () use ($dosenList, $validated, $role, &$created, &$linked, &$skipped, &$hashCache) {
            foreach ($dosenList as $dosen) {
                $email = strtolower(trim((string) $dosen->email));
                if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped[] = "{$dosen->nama}: belum punya email";

                    continue;
                }

                $existing = User::whereRaw('LOWER(email) = ?', [$email])->first();
                if ($existing) {
                    if (Dosen::where('user_id', $existing->id)->exists()) {
                        $skipped[] = "{$dosen->nama}: email {$email} sudah dipakai dosen lain";

                        continue;
                    }
                    $dosen->update(['user_id' => $existing->id]);
                    if ($role) {
                        $existing->assignRole($role);
                    }
                    $linked++;

                    continue;
                }

                $password = $validated['password_mode'] === 'custom'
                    ? $validated['password']
                    : ($dosen->nidn_nidk ?: $dosen->kode_dosen);
                if (! $password || mb_strlen($password) < 8) {
                    $skipped[] = "{$dosen->nama}: NIDN/Kode Dosen kosong atau kurang dari 8 karakter (pakai opsi password yang ditentukan)";

                    continue;
                }

                $user = User::create([
                    'name' => $dosen->nama,
                    'email' => $email,
                    'password' => $hashCache[$password] ??= Hash::make($password),
                    'role' => 'user',
                    'is_active' => true,
                ]);
                if ($role) {
                    $user->assignRole($role);
                }
                $dosen->update(['user_id' => $user->id]);
                $created++;
            }
        });

        if (! $created && ! $linked) {
            return back()->with('error', 'Tidak ada akun yang dibuat.')->with('akun_skipped', $skipped);
        }

        $msg = "{$created} akun dibuat".($linked ? ", {$linked} dosen ditautkan ke akun yang sudah ada" : '').'.';
        $msg .= $role ? " Role: {$role->name}." : ' Role belum diatur — atur di menu Pengguna.';
        if ($created && $validated['password_mode'] === 'nidn') {
            $msg .= ' Password awal = NIDN/NIDK (atau Kode Dosen). Minta dosen segera menggantinya.';
        }
        $remaining = $totalPending - $dosenList->count();
        if ($remaining > 0) {
            $msg .= " Masih ada {$remaining} dosen lagi — klik \"Buatkan Akun\" sekali lagi untuk melanjutkan.";
        }

        $redirect = back()->with($created || $linked ? 'success' : 'error', $msg);

        return $skipped ? $redirect->with('akun_skipped', $skipped) : $redirect;
    }
}
