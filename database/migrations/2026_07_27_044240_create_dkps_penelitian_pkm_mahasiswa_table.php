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
        Schema::create('dkps_penelitian_pkm_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('jenis', ['penelitian', 'pkm']);
            $table->string('nama_dtps')->nullable();
            $table->string('judul_tema');
            $table->text('nim_nama_mahasiswa')->nullable();
            $table->string('peran_mahasiswa')->nullable();
            $table->enum('tahun_relatif', ['TS-2', 'TS-1', 'TS']);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_penelitian_pkm_mahasiswa');
    }
};
