<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Deprecated: the Statistik links now live in the "Laporan" group built by
 * AmiMenuSeeder (with a valid `tipe`). Kept so `db:seed --class=StatistikMenuSeeder`
 * still does the right thing.
 */
class StatistikMenuSeeder extends Seeder
{
    public function run(): void
    {
        AmiMenuSeeder::rebuild();

        $this->command?->info('Statistik menu is now part of AmiMenuSeeder (Laporan group).');
    }
}
