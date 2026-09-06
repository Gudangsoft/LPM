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
        Schema::create('dosen_tetap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->string('nama');
            $table->string('nidn_nidk')->nullable();
            $table->string('nuptk')->nullable();
            $table->string('pendidikan_magister_bidang')->nullable();
            $table->string('pendidikan_doktor_bidang')->nullable();
            $table->string('bidang_keahlian')->nullable();
            $table->enum('jabatan_akademik', ['tenaga_pengajar', 'asisten_ahli', 'lektor', 'lektor_kepala', 'guru_besar'])->nullable();
            $table->string('no_sertifikat_pendidik')->nullable();
            $table->text('mk_diampu_ps_diakreditasi')->nullable();
            $table->text('mk_diampu_ps_lain')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_tetap');
    }
};
