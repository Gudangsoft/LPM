<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_kepuasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->nullable()->constrained('prodi')->cascadeOnDelete();
            $table->enum('jenis_responden', ['mahasiswa', 'dosen', 'tendik', 'alumni', 'pengguna_lulusan']);
            $table->string('judul_survei');
            $table->string('tahun_akademik', 20);
            $table->enum('semester', ['ganjil', 'genap'])->nullable();
            $table->unsignedInteger('jumlah_responden')->default(0);
            $table->decimal('rata_rata_skor', 5, 2)->nullable();
            $table->decimal('skala_maksimal', 5, 2)->default(4);
            $table->text('ringkasan_hasil')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_kepuasan');
    }
};
