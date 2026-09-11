<?php

use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Fix a numbering gap under SPMI: after AMI was promoted to its own
     * top-level group, "P-3 Pengendalian" (whose only children were AMI
     * pages) disappeared entirely, so the sidebar jumped straight from
     * "E-Evaluasi (Non-AMI)" to "P-4 Peningkatan". Restore P-3 as a set of
     * shortcut links to the same Tindak Lanjut / Verifikasi RTL / Monitoring
     * pages already under the AMI group, so the PPEPP numbering (P-1..P-4)
     * is complete again under SPMI.
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
