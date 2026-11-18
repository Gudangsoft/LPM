<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Siakad\SiakadException;
use App\Services\Siakad\SiakadSync;
use App\Models\Prodi;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Tombol "Tarik dari SIAKAD" di halaman Program Studi & Data Dosen (khusus admin). */
class SiakadController extends Controller
{
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
