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
        Schema::create('dkps_pengembangan_kompetensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('person_type', ['dosen', 'tendik']);
            $table->foreignId('dosen_tetap_id')->nullable()->constrained('dosen_tetap')->cascadeOnDelete();
            $table->foreignId('tenaga_kependidikan_id')->nullable()->constrained('tenaga_kependidikan')->cascadeOnDelete();
            $table->string('deskripsi_kegiatan');
            $table->string('tempat')->nullable();
            $table->string('waktu_pelaksanaan')->nullable();
            $table->text('manfaat')->nullable();
            $table->string('bukti_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_pengembangan_kompetensi');
    }
};
