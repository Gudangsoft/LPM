<?php

use App\Models\Pengaturan;
use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * "Aturan AMI": reuse the generic Pengaturan settings system (new tab
     * `?tab=ami` on the existing settings page) instead of a new module.
     * Seed sane defaults so the tab isn't empty on first visit, and point
     * the sidebar item at the real settings page instead of coming-soon.
     */
    public function up(): void
    {
        $defaults = [
            'ami_reminder_evaluasi_diri_hari' => ['7', 'number', 'ami', 'Pengingat Evaluasi Diri (hari)'],
            'ami_batas_hari_rtl' => ['14', 'number', 'ami', 'Batas Waktu RTL Standar (hari)'],
            'ami_skala_nilai_min' => ['1', 'number', 'ami', 'Nilai Minimum'],
            'ami_skala_nilai_maks' => ['4', 'number', 'ami', 'Nilai Maksimum'],
            'ami_wajib_bukti_rtl' => ['1', 'boolean', 'ami', 'Wajibkan Bukti Tindak Lanjut'],
            'ami_deskripsi_umum' => [
                'Isi evaluasi diri secara jujur dan objektif berdasarkan bukti yang tersedia. '
                . 'Lampirkan dokumen pendukung pada setiap butir instrumen yang relevan.',
                'text', 'ami', 'Catatan/Instruksi Umum AMI',
            ],
        ];

        foreach ($defaults as $key => [$value, $type, $group, $label]) {
            if (Pengaturan::where('key', $key)->doesntExist()) {
                Pengaturan::set($key, $value, $type, $group, $label);
            }
        }

        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Data pengaturan & menu - reversal via re-running the seeders.
    }
};
