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
        Schema::create('dkps_kegiatan_luar_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->string('nama_tema_kegiatan');
            $table->string('dosen_pembina')->nullable();
            $table->date('tanggal')->nullable();
            $table->enum('tahun_relatif', ['TS-2', 'TS-1', 'TS']);
            $table->string('bukti_file')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_kegiatan_luar_kelas');
    }
};
