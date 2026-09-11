<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasi_pembelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodi')->cascadeOnDelete();
            $table->string('mata_kuliah');
            $table->string('dosen_pengampu');
            $table->string('tahun_akademik', 20);
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->enum('kesesuaian_rps', ['sesuai', 'kurang_sesuai', 'tidak_sesuai'])->nullable();
            $table->text('kendala')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->enum('status', ['belum_dievaluasi', 'dievaluasi'])->default('belum_dievaluasi');
            $table->foreignId('dievaluasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dievaluasi_pada')->nullable();
            $table->timestamps();

            $table->index(['prodi_id', 'tahun_akademik', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_pembelajaran');
    }
};
