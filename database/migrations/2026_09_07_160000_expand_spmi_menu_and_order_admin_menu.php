<?php

use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Expand the SPMI/AMI sidebar into the full PPEPP structure (real features +
     * "coming soon" placeholders) and normalise the order of every top-level
     * admin menu item by function.
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
