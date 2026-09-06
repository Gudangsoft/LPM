<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akreditasi;
use App\Models\TemuanAmi;
use App\Models\TindakLanjut;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LaporanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:laporan-ami.view', only: ['index']),
            new Middleware('permission:laporan.export', only: ['exportTemuan', 'exportTindakLanjut', 'exportAkreditasi']),
        ];
    }

    public function index()
    {
        return view('admin.laporan.index');
    }

    public function exportTemuan()
    {
        $temuans = TemuanAmi::with(['jadwalAmi.prodi', 'jadwalAmi.periodeAmi', 'auditor.user'])
            ->orderByDesc('created_at')
            ->get();

        return $this->streamCsv('laporan-temuan-ami.csv', function ($handle) use ($temuans) {
            fputcsv($handle, ['ID', 'Program Studi', 'Periode', 'Tanggal Audit', 'Standar', 'Kategori', 'Deskripsi', 'Status', 'Batas Tindak Lanjut', 'Auditor']);

            foreach ($temuans as $t) {
                fputcsv($handle, [
                    $t->id,
                    $t->jadwalAmi->prodi->nama ?? '-',
                    $t->jadwalAmi->periodeAmi->nama ?? '-',
                    optional($t->jadwalAmi?->tanggal_audit)->format('Y-m-d'),
                    $t->standar,
                    $t->kategori,
                    $t->deskripsi,
                    $t->status,
                    optional($t->batas_tindak_lanjut)->format('Y-m-d'),
                    $t->auditor->user->name ?? '-',
                ]);
            }
        });
    }

    public function exportTindakLanjut()
    {
        $tindakLanjuts = TindakLanjut::with(['temuanAmi.jadwalAmi.prodi', 'user', 'reviewer'])
            ->orderByDesc('created_at')
            ->get();

        return $this->streamCsv('laporan-tindak-lanjut.csv', function ($handle) use ($tindakLanjuts) {
            fputcsv($handle, ['ID', 'Temuan', 'Program Studi', 'Diajukan Oleh', 'Tanggal Submit', 'Status', 'Direview Oleh', 'Tanggal Review', 'Catatan Reviewer']);

            foreach ($tindakLanjuts as $tl) {
                fputcsv($handle, [
                    $tl->id,
                    $tl->temuanAmi->standar ?? '-',
                    $tl->temuanAmi->jadwalAmi->prodi->nama ?? '-',
                    $tl->user->name ?? '-',
                    optional($tl->tanggal_submit)->format('Y-m-d'),
                    $tl->status,
                    $tl->reviewer->name ?? '-',
                    optional($tl->reviewed_at)->format('Y-m-d H:i'),
                    $tl->catatan_reviewer,
                ]);
            }
        });
    }

    public function exportAkreditasi()
    {
        $akreditasis = Akreditasi::with('prodi')->orderBy('tanggal_kadaluarsa')->get();

        return $this->streamCsv('laporan-akreditasi.csv', function ($handle) use ($akreditasis) {
            fputcsv($handle, ['ID', 'Program Studi', 'Jenjang', 'Lembaga', 'Peringkat', 'Nomor SK', 'Tanggal SK', 'Berlaku Hingga', 'Status']);

            foreach ($akreditasis as $a) {
                fputcsv($handle, [
                    $a->id,
                    $a->prodi->nama ?? '-',
                    $a->prodi->jenjang ?? '-',
                    $a->lembaga,
                    $a->peringkat,
                    $a->nomor_sk,
                    optional($a->tanggal_sk)->format('Y-m-d'),
                    optional($a->tanggal_kadaluarsa)->format('Y-m-d'),
                    $a->status,
                ]);
            }
        });
    }

    private function streamCsv(string $filename, \Closure $writeRows)
    {
        return response()->streamDownload(function () use ($writeRows) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM so Excel opens Indonesian characters correctly.
            fwrite($handle, "\xEF\xBB\xBF");
            $writeRows($handle);
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
