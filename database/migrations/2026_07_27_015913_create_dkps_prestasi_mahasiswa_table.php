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
        Schema::create('dkps_prestasi_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->string('nama_kegiatan');
            $table->enum('jenis_prestasi', ['akademik', 'non_akademik']);
            $table->date('tanggal_perolehan')->nullable();
            $table->enum('tingkat', ['wilayah_lokal', 'nasional', 'internasional']);
            $table->string('prestasi_dicapai')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_prestasi_mahasiswa');
    }
};
