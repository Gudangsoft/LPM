<?php

use App\Models\JenisDokumen;
use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Turn the "Kebijakan SPMI" / "Manual SPMI" / "Formulir SPMI" coming-soon
     * placeholders under P-1 Penetapan into real links into the existing
     * Dokumen module (upload, versioning, draft/submit/approve workflow),
     * pre-filtered to their matching jenis_dokumen. Also backfills a short
     * description on each jenis_dokumen row (used as a page subtitle).
     */
    public function up(): void
    {
        $descriptions = [
            'kebijakan-spmi' => 'Dokumen yang memuat garis besar bagaimana perguruan tinggi memahami, merancang, dan melaksanakan penjaminan mutu internal.',
            'standar-spmi' => 'Dokumen berisi kriteria, ukuran, atau spesifikasi mutu yang wajib dipenuhi dan terus dilampaui.',
            'manual-spmi' => 'Dokumen berisi petunjuk praktis penetapan, pelaksanaan, evaluasi, pengendalian, dan peningkatan standar mutu (siklus PPEPP).',
            'formulir' => 'Formulir/rekaman yang digunakan untuk mencatat pelaksanaan setiap standar mutu.',
            'sop' => 'Prosedur atau langkah kerja baku untuk pelaksanaan kegiatan penjaminan mutu.',
            'laporan' => 'Laporan hasil pelaksanaan kegiatan penjaminan mutu, termasuk audit dan evaluasi.',
            'panduan' => 'Panduan teknis pelaksanaan kegiatan tertentu di lingkungan penjaminan mutu.',
            'sk-surat' => 'Surat Keputusan dan surat resmi lain terkait penjaminan mutu.',
        ];

        foreach ($descriptions as $slug => $deskripsi) {
            JenisDokumen::where('slug', $slug)
                ->where(fn ($q) => $q->whereNull('deskripsi')->orWhere('deskripsi', ''))
                ->update(['deskripsi' => $deskripsi]);
        }

        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Menu + descriptive data only - reversal is done by re-running the seeders.
    }
};
