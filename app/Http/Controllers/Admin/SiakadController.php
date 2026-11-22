<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DkpsSubmission;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Prodi;
use App\Services\Siakad\SiakadClient;
use App\Services\Siakad\SiakadException;
use App\Services\Siakad\SiakadSync;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/** Menu "Tarik Data SIAKAD" + tombol di halaman Program Studi & Data Dosen (khusus admin). */
class SiakadController extends Controller
{
    /** Layanan API yang boleh dicek kolomnya dari halaman (lihat dokumen API SIAKAD). */
    public const CEK_ACTS = ['get_token', 'get_fakultas', 'get_prodi', 'get_dosen', 'get_mahasiswa', 'get_kurikulum', 'get_matakuliah'];

    public function index(Request $request)
    {
        $cek = null;
        if ($request->filled('cek')) {
            $request->validate(['cek' => 'in:'.implode(',', self::CEK_ACTS), 'progdi' => 'nullable|string|max:30']);
            @set_time_limit(300);
            try {
                $params = ['act' => $request->cek, '--limit' => 3];
                if ($request->filled('progdi')) {
                    $params['--progdi'] = $request->progdi;
                }
                Artisan::call('siakad:test', $params);
                $cek = ['act' => $request->cek, 'output' => Artisan::output()];
            } catch (\Throwable $e) {
                $cek = ['act' => $request->cek, 'output' => $this->pesanGagal($e)];
            }
        }

        $last = fn ($k) => Cache::get("siakad:last_sync:{$k}", 'belum pernah');

        return view('admin.siakad.index', [
            'configured' => app(SiakadClient::class)->isConfigured(),
            'prodis' => Prodi::orderBy('jenjang')->orderBy('nama')->get(['id', 'nama', 'jenjang', 'kode', 'kode_siakad']),
            'dkpsList' => DkpsSubmission::with('prodi:id,nama,jenjang')->orderByDesc('tahun_ts_akhir')->get(['id', 'prodi_id', 'tahun_ts_awal', 'tahun_ts_akhir']),
            'last' => ['prodi' => $last('prodi'), 'dosen' => $last('dosen'), 'kurikulum' => $last('kurikulum'), 'mahasiswa' => $last('mahasiswa'), 'matakuliah' => $last('matakuliah')],
            'jumlah' => ['prodi' => Prodi::count(), 'prodi_siakad' => Prodi::whereNotNull('kode_siakad')->count(), 'dosen' => Dosen::count(),
                'mk' => Schema::hasTable('mata_kuliah') ? MataKuliah::count() : 0],
            'cek' => $cek,
        ]);
    }

    /**
     * Daftar mata kuliah per prodi (tombol di Capaian Pembelajaran, Evaluasi Pembelajaran,
     * Review RPS, dan halaman Tarik Data SIAKAD). Admin: prodi mana pun atau semua prodi;
     * kaprodi: hanya prodinya sendiri.
     */
    public function syncMataKuliah(Request $request, SiakadSync $sync)
    {
        @set_time_limit(600);
        $user = $request->user();
        $request->validate(['prodi_id' => ($user->isAdmin() ? 'nullable' : 'required').'|exists:prodi,id'], ['prodi_id.required' => 'Pilih program studi.']);
        $prodi = $request->filled('prodi_id') ? Prodi::find($request->prodi_id) : null;
        abort_unless($user->isAdmin() || ($prodi && (int) $prodi->kaprodi_id === (int) $user->id), 403, 'Anda hanya dapat menarik mata kuliah program studi Anda sendiri.');

        try {
            $r = $sync->syncMataKuliah($prodi);
        } catch (\Throwable $e) {
            return back()->with('error', $this->pesanGagal($e));
        }

        if ($r['status'] !== 'ok') {
            $redirect = back()->with('error', 'Tarik mata kuliah gagal: '.$r['message']);

            return $r['skipped'] ? $redirect->with('import_errors', array_slice($r['skipped'], 0, 100))->with('import_errors_title', 'Detail dari SIAKAD:') : $redirect;
        }

        $redirect = back()->with('success', 'Tarik mata kuliah selesai'.($prodi ? " ({$prodi->jenjang} {$prodi->nama})" : '').": {$r['total']} data dari SIAKAD, "
            ."{$r['created']} ditambahkan, {$r['updated']} diperbarui. Mata kuliah kini muncul sebagai pilihan saat mengisi form."
            .($r['skipped'] ? ' '.count($r['skipped']).' catatan (lihat daftar).' : ''));

        return $r['skipped']
            ? $redirect->with('import_errors', array_slice($r['skipped'], 0, 100))->with('import_errors_title', 'Catatan dari SIAKAD:')
            : $redirect;
    }

    public function syncKurikulum(Request $request, SiakadSync $sync)
    {
        return $this->syncDkps($request, fn (DkpsSubmission $dkp) => $sync->syncKurikulum($dkp), 'mata kuliah', 'Tabel Kurikulum');
    }

    public function syncMahasiswa(Request $request, SiakadSync $sync)
    {
        return $this->syncDkps($request, fn (DkpsSubmission $dkp) => $sync->syncMahasiswa($dkp), 'mahasiswa', 'Tabel Kualitas Input Mahasiswa');
    }

    private function syncDkps(Request $request, callable $run, string $apa, string $tabel)
    {
        @set_time_limit(600);
        $request->validate(['dkps_id' => 'required|exists:dkps_submissions,id'], ['dkps_id.required' => 'Pilih DKPS tujuan.']);
        $dkp = DkpsSubmission::with('prodi')->findOrFail($request->dkps_id);
        try {
            $r = $run($dkp);
        } catch (\Throwable $e) {
            return back()->with('error', $this->pesanGagal($e));
        }

        if ($r['status'] !== 'ok') {
            $redirect = back()->with('error', "Tarik {$apa} gagal: {$r['message']}");

            return $r['skipped'] ? $redirect->with('import_errors', array_slice($r['skipped'], 0, 100))->with('import_errors_title', 'Detail dari SIAKAD:') : $redirect;
        }

        $redirect = back()->with('success', "Tarik {$apa} selesai ({$dkp->prodi?->jenjang} {$dkp->prodi?->nama}, TS {$dkp->tahun_ts_awal}/{$dkp->tahun_ts_akhir}): "
            ."{$r['total']} data dari SIAKAD, {$r['created']} ditambahkan, {$r['updated']} diperbarui di DKPS {$tabel}."
            .($r['skipped'] ? ' '.count($r['skipped']).' dilewati (lihat daftar).' : ''));

        return $r['skipped']
            ? $redirect->with('import_errors', array_slice($r['skipped'], 0, 100))->with('import_errors_title', 'Data dari SIAKAD yang dilewati:')
            : $redirect;
    }

    public function syncProdi(SiakadSync $sync)
    {
        @set_time_limit(600);
        try {
            $r = $sync->syncProdi();
        } catch (\Throwable $e) {
            return back()->with('error', $this->pesanGagal($e));
        }

        $redirect = back()->with('success', "Sinkron SIAKAD selesai: {$r['total']} prodi di SIAKAD, {$r['created']} ditambahkan, {$r['updated']} diperbarui."
            .($r['skipped'] ? ' '.count($r['skipped']).' dilewati (lihat daftar).' : ''));

        return $r['skipped']
            ? $redirect->with('import_errors', $r['skipped'])->with('import_errors_title', 'Data dari SIAKAD yang dilewati:')
            : $redirect;
    }

    public function syncDosen(Request $request, SiakadSync $sync)
    {
        @set_time_limit(600);
        $request->validate([
            'prodi_id' => 'nullable|exists:prodi,id',
            'email_domain' => ['nullable', 'string', 'max:100', 'regex:/^@?[a-z0-9-]+(\.[a-z0-9-]+)+$/i'],
        ], ['email_domain.regex' => 'Domain email tidak valid (contoh: unukase.ac.id).']);
        $defaultProdi = $request->filled('prodi_id') ? Prodi::find($request->prodi_id) : null;
        $emailDomain = $request->boolean('isi_email') ? ($request->input('email_domain') ?: 'unukase.ac.id') : null;
        try {
            $r = $sync->syncDosen($defaultProdi, $emailDomain);
        } catch (\Throwable $e) {
            return back()->with('error', $this->pesanGagal($e));
        }

        if ($r['status'] !== 'ok') {
            $redirect = back()->with('error', 'Sinkron dosen gagal: '.$r['message']);

            return $r['errors']
                ? $redirect->with('import_errors', array_slice($r['errors'], 0, 100))->with('import_errors_title', 'Detail dari SIAKAD:')
                : $redirect;
        }

        $p = $r['prodi'] ?? null;
        $msg = "Sinkron SIAKAD selesai: {$r['total']} dosen di SIAKAD, {$r['created']} ditambahkan, {$r['updated']} diperbarui.";
        if ($r['skipped']) {
            $msg .= " {$r['skipped']} dilewati (lihat daftar).";
        }
        if ($p && $p['created']) {
            $msg .= " Program Studi baru dari SIAKAD: {$p['created']}.";
        }
        $redirect = back()->with('success', $msg);
        $errors = array_merge(
            array_map(fn ($s) => "Prodi dilewati: {$s}", $p['skipped'] ?? []),
            $r['errors']
        );
        if ($errors) {
            $redirect->with('import_errors', array_slice($errors, 0, 100))
                ->with('import_errors_title', 'Data dari SIAKAD yang dilewati:');
        }

        return $r['warnings'] ? $redirect->with('import_warnings', array_slice($r['warnings'], 0, 100)) : $redirect;
    }

    /** Pesan untuk admin; galat tak terduga tetap dicatat ke log, tanpa memunculkan halaman 500. */
    private function pesanGagal(\Throwable $e): string
    {
        if ($e instanceof SiakadException) {
            return $e->getMessage();
        }

        try {
            report($e);
        } catch (\Throwable) {
            // log tidak bisa ditulis (mis. izin folder storage) – abaikan
        }

        $detail = $e instanceof QueryException
            ? ($e->errorInfo[2] ?? $e->getMessage())
            : $e->getMessage();

        return 'Sinkron SIAKAD gagal karena kesalahan sistem: '.Str::limit($detail, 300)
            .' ['.class_basename($e).' di '.basename($e->getFile()).':'.$e->getLine().']';
    }
}
