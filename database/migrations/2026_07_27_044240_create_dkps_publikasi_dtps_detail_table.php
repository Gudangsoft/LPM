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
        Schema::create('dkps_publikasi_dtps_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->foreignId('dosen_tetap_id')->nullable()->constrained('dosen_tetap')->nullOnDelete();
            $table->text('judul_artikel');
            $table->string('nama_penulis')->nullable();
            $table->enum('penulis_peran', ['penulis_pertama', 'corresponding_author']);
            $table->enum('jenis_jurnal', ['nasional', 'internasional']);
            $table->enum('terindeks', [
                'scopus_q1', 'scopus_q2', 'scopus_q3', 'scopus_q4', 'wos',
                'sinta_1', 'sinta_2', 'sinta_3', 'sinta_4',
            ]);
            $table->date('tanggal_terbit')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_publikasi_dtps_detail');
    }
};
