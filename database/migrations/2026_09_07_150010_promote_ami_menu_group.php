<?php

use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Angkat AMI menjadi grup sidebar top-level (sejajar SPMI) dengan seluruh
     * menu operasional audit rata di satu tingkat. Akreditasi juga jadi grup
     * top-level sendiri. SPMI menyisakan sisi kebijakan/dokumen (PPEPP).
     */
    public function up(): void
    {
        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Menu data only - reversal via re-running the seeders.
    }
};
