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
        Schema::create('dkps_beban_kerja_dtps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->foreignId('dosen_tetap_id')->constrained('dosen_tetap')->cascadeOnDelete();
            $table->decimal('sks_pendidikan_ps', 5, 2)->nullable();
            $table->decimal('sks_pendidikan_ps_lain_dalam', 5, 2)->nullable();
            $table->decimal('sks_pendidikan_ps_lain_luar', 5, 2)->nullable();
            $table->decimal('sks_penelitian', 5, 2)->nullable();
            $table->decimal('sks_pkm', 5, 2)->nullable();
            $table->decimal('sks_tugas_tambahan', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_beban_kerja_dtps');
    }
};
