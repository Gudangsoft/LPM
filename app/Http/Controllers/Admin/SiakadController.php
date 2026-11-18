<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Siakad\SiakadException;
use App\Services\Siakad\SiakadSync;

/** Tombol "Tarik dari SIAKAD" di halaman Program Studi & Data Dosen (khusus admin). */
class SiakadController extends Controller
{
    public function syncProdi(SiakadSync $sync)
    {
        @set_time_limit(300);
        try {
            $r = $sync->syncProdi();
        } catch (SiakadException $e) {
            return back()->with('error', $e->getMessage());
        }

        $redirect = back()->with('success', "Sinkron SIAKAD selesai: {$r['total']} prodi di SIAKAD, {$r['created']} ditambahkan, {$r['updated']} diperbarui."
            .($r['skipped'] ? ' '.count($r['skipped']).' dilewati (lihat daftar).' : ''));

        return $r['skipped']
            ? $redirect->with('import_errors', $r['skipped'])->with('import_errors_title', 'Data dari SIAKAD yang dilewati:')
            : $redirect;
    }

    public function syncDosen(SiakadSync $sync)
    {
        @set_time_limit(300);
        try {
            $r = $sync->syncDosen();
        } catch (SiakadException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($r['status'] !== 'ok') {
            $redirect = back()->with('error', 'Sinkron dosen gagal: '.$r['message']);

            return $r['errors'] ? $redirect->with('import_errors', array_slice($r['errors'], 0, 100)) : $redirect;
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
}
