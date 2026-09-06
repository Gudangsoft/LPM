<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dkps_kualitas_input', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('tahun_relatif', ['TS-4', 'TS-3', 'TS-2', 'TS-1', 'TS']);
            $table->unsignedInteger('daya_tampung')->nullable();
            $table->unsignedInteger('pendaftar')->nullable();
            $table->unsignedInteger('lulus_seleksi')->nullable();
            $table->unsignedInteger('mahasiswa_baru_reguler')->nullable();
            $table->unsignedInteger('mahasiswa_baru_transfer')->nullable();
            $table->unsignedInteger('mahasiswa_aktif_reguler')->nullable();
            $table->unsignedInteger('mahasiswa_aktif_transfer')->nullable();
            $table->unsignedInteger('mahasiswa_pddikti')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_kualitas_input');
    }
};
