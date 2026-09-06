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
        Schema::create('dkps_publikasi_dtps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('media_publikasi', [
                'jurnal_nasional_tidak_terakreditasi', 'jurnal_nasional_terakreditasi',
                'jurnal_internasional_karya_monumental_nasional', 'jurnal_internasional_bereputasi_karya_monumental_internasional',
                'seminar_wilayah_lokal_pt', 'seminar_nasional', 'seminar_internasional',
                'media_massa_wilayah', 'media_massa_nasional', 'media_massa_internasional',
                'buku_isbn_book_chapter', 'paten_paten_sederhana',
            ]);
            $table->unsignedInteger('jumlah_ts2')->nullable();
            $table->unsignedInteger('jumlah_ts1')->nullable();
            $table->unsignedInteger('jumlah_ts')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_publikasi_dtps');
    }
};
