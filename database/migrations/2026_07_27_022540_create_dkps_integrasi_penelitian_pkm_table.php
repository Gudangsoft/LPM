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
        Schema::create('dkps_integrasi_penelitian_pkm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->foreignId('dosen_tetap_id')->nullable()->constrained('dosen_tetap')->nullOnDelete();
            $table->string('judul_penelitian_pkm');
            $table->string('mata_kuliah')->nullable();
            $table->enum('bentuk_integrasi', [
                'tambahan_materi', 'studi_kasus', 'bab_buku_ajar', 'bahan_ajar', 'bentuk_lain',
            ]);
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
        Schema::dropIfExists('dkps_integrasi_penelitian_pkm');
    }
};
