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
        Schema::create('dkps_rekognisi_dtps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->foreignId('dosen_tetap_id')->constrained('dosen_tetap')->cascadeOnDelete();
            $table->string('bidang_keahlian')->nullable();
            $table->string('deskripsi_rekognisi');
            $table->enum('jenis_rekognisi', [
                'visiting_lecturer', 'keynote_speaker', 'editor_mitra_bestari',
                'staf_ahli_narasumber', 'penghargaan_prestasi',
            ]);
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->enum('tingkat', ['wilayah_lokal', 'nasional', 'internasional']);
            $table->string('bukti_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_rekognisi_dtps');
    }
};
