<?php

use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Rebuild the "PENJAMINAN MUTU" part of the admin sidebar as a PPEPP tree
     * (SPMI > P-1..P-4 / E-Evaluasi > groups > links). Only that section is
     * touched; the rest of the sidebar and the website menu are untouched.
     */
    public function up(): void
    {
        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Data restructure - rebuilding the old flat layout is handled by
        // re-running the seeders, so this is intentionally a no-op.
    }
};
