<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Daftar mata kuliah per prodi (ditarik dari SIAKAD get_kurikulum/get_matakuliah).
 * Dipakai sebagai pilihan Mata Kuliah di Capaian Pembelajaran, Evaluasi Pembelajaran,
 * dan Review RPS.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodi')->cascadeOnDelete();
            $table->string('kode_mk', 50)->nullable();
            $table->string('nama');
            $table->unsignedTinyInteger('semester')->nullable();
            $table->decimal('sks', 4, 1)->nullable();
            $table->timestamps();

            $table->index(['prodi_id', 'kode_mk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};
