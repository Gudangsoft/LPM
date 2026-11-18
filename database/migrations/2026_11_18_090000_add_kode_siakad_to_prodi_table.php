<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kode prodi di SIAKAD (bisa berbeda dari kode SPMI, mis. "55201" vs "TI").
 * Diisi saat sinkron prodi; dipakai mencocokkan HomeBase dosen dari SIAKAD.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prodi', function (Blueprint $table) {
            $table->string('kode_siakad', 50)->nullable()->unique()->after('kode');
        });
    }

    public function down(): void
    {
        Schema::table('prodi', function (Blueprint $table) {
            $table->dropUnique(['kode_siakad']);
            $table->dropColumn('kode_siakad');
        });
    }
};
