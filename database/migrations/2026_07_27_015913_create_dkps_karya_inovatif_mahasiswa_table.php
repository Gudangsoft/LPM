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
        Schema::create('dkps_karya_inovatif_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('kategori', ['paten', 'buku_isbn', 'karya_seni', 'publikasi_jurnal']);
            $table->string('nim')->nullable();
            $table->string('nama_mahasiswa');
            $table->string('judul');
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->string('keterangan')->nullable();
            $table->enum('peringkat_jurnal', [
                'sinta_1', 'sinta_2', 'sinta_3', 'sinta_4', 'sinta_5',
                'jurnal_internasional', 'jurnal_internasional_bereputasi',
            ])->nullable();
            $table->string('tautan')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_karya_inovatif_mahasiswa');
    }
};
