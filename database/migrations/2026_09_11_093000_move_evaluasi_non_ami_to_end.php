<?php

use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * "E-Evaluasi (Non-AMI)" sat between P-2 and P-3 (its standard PPEPP
     * position), but every one of its items is still a placeholder. Move it
     * to the end of the SPMI list so the live P-1..P-4 flow isn't
     * interrupted by an all-placeholder block in the middle.
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
