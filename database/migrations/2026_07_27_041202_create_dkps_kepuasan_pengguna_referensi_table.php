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
        Schema::create('dkps_kepuasan_pengguna_referensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('tahun_lulus', ['TS-4', 'TS-3', 'TS-2']);
            $table->unsignedInteger('jumlah_lulusan')->nullable();
            $table->unsignedInteger('jumlah_tanggapan_terlacak')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_kepuasan_pengguna_referensi');
    }
};
