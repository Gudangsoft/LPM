<?php

use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Move the "PENJAMINAN MUTU" section (SPMI + Laporan) to the top of the
     * admin sidebar, right below Dashboard. Handled by re-running the rebuild,
     * which now also normalises the root menu order.
     */
    public function up(): void
    {
        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Menu ordering only - reversal is done by re-running the seeders.
    }
};
