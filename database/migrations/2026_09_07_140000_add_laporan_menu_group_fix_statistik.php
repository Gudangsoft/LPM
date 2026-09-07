<?php

use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Rebuild the PENJAMINAN MUTU sidebar branch: adds a top-level "Laporan"
     * group (Laporan AMI, Data Statistik, Grafik) and removes the broken
     * "Statistik" rows (invalid `tipe`) left by the old StatistikMenuSeeder.
     */
    public function up(): void
    {
        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Data restructure - reversal is done by re-running the seeders.
    }
};
