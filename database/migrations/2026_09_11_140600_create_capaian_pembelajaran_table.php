<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capaian_pembelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodi')->cascadeOnDelete();
            $table->string('mata_kuliah');
            $table->string('cpmk');
            $table->text('deskripsi_cpmk')->nullable();
            $table->string('tahun_akademik', 20);
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->decimal('target_capaian', 5, 2);
            $table->decimal('realisasi_capaian', 5, 2)->nullable();
            $table->enum('status', ['belum_dievaluasi', 'tercapai', 'tidak_tercapai'])->default('belum_dievaluasi');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['prodi_id', 'tahun_akademik', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capaian_pembelajaran');
    }
};
