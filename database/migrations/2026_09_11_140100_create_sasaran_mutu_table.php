<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sasaran_mutu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standar_mutu_id')->constrained('standar_mutu')->cascadeOnDelete();
            $table->foreignId('prodi_id')->nullable()->constrained('prodi')->cascadeOnDelete();
            $table->string('tahun_akademik', 20);
            $table->text('uraian_sasaran');
            $table->string('indikator')->nullable();
            $table->string('target')->nullable();
            $table->string('satuan')->nullable();
            $table->string('realisasi')->nullable();
            $table->enum('status', ['belum_dievaluasi', 'tercapai', 'tidak_tercapai'])->default('belum_dievaluasi');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['standar_mutu_id', 'tahun_akademik']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sasaran_mutu');
    }
};
