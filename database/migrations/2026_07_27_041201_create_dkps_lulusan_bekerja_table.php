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
        Schema::create('dkps_lulusan_bekerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('tahun_lulus', ['TS-4', 'TS-3', 'TS-2']);
            $table->unsignedInteger('jumlah_lulusan')->nullable();
            $table->unsignedInteger('jumlah_terlacak')->nullable();
            $table->unsignedInteger('bekerja_sesuai_bidang')->nullable();
            $table->unsignedInteger('usaha_mandiri')->nullable();
            $table->unsignedInteger('studi_lanjut_s2')->nullable();
            $table->unsignedInteger('mengikuti_ppg')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_lulusan_bekerja');
    }
};
