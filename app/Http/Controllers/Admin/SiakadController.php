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

        return $r['skipped'] ? $redirect->with('import_errors', $r['skipped']) : $redirect;
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

        $msg = "Sinkron SIAKAD selesai: {$r['total']} dosen di SIAKAD, {$r['created']} ditambahkan, {$r['updated']} diperbarui.";
        if ($r['skipped']) {
            $msg .= " {$r['skipped']} dilewati karena datanya tidak lengkap (lihat daftar).";
        }
        $redirect = back()->with('success', $msg);
        if ($r['errors']) {
            $redirect->with('import_errors', array_slice($r['errors'], 0, 100));
        }

        return $r['warnings'] ? $redirect->with('import_warnings', array_slice($r['warnings'], 0, 100)) : $redirect;
    }
}
