<?php

use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * "Buku Panduan" is a cross-module application user guide, not an SPMI
     * policy document - it doesn't belong under P-1 Penetapan. Remove it from
     * the sidebar; it's now reached via the "?" help icon in the admin header.
     */
    public function up(): void
    {
        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Menu data only - reversal is done by re-running the seeders.
    }
};
