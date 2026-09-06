<?php

namespace Database\Seeders;

use App\Models\StandarMutu;
use App\Models\TemuanAmi;
use Illuminate\Database\Seeder;

class StandarMutuSeeder extends Seeder
{
    /**
     * Seeds the 9 BAN-PT standards (same strings previously hardcoded in
     * TemuanAmiController) and backfills standar_mutu_id on existing
     * temuan_ami rows whose free-text `standar` matches exactly.
     * Safe to re-run (idempotent).
     */
    public function run(): void
    {
        $standards = [
            'Visi, Misi, Tujuan dan Strategi',
            'Tata Pamong, Tata Kelola dan Kerjasama',
            'Mahasiswa',
            'Sumber Daya Manusia',
            'Keuangan, Sarana dan Prasarana',
            'Pendidikan',
            'Penelitian',
            'Pengabdian kepada Masyarakat',
            'Luaran dan Capaian Tridharma',
        ];

        foreach ($standards as $index => $nama) {
            $kode = (string) ($index + 1);

            StandarMutu::updateOrCreate(
                ['nama' => "Standar {$kode} - {$nama}"],
                ['kode' => $kode, 'urutan' => $index + 1, 'is_active' => true]
            );
        }

        // Backfill existing free-text temuan_ami.standar rows to the new FK.
        StandarMutu::all()->each(function (StandarMutu $standar) {
            TemuanAmi::where('standar', $standar->nama)
                ->whereNull('standar_mutu_id')
                ->update(['standar_mutu_id' => $standar->id]);
        });
    }
}
