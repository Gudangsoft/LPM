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
        Schema::create('dkps_pembimbingan_magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->foreignId('dosen_tetap_id')->constrained('dosen_tetap')->cascadeOnDelete();
            $table->unsignedInteger('jml_mhs_ts2')->nullable();
            $table->unsignedInteger('jml_mhs_ts1')->nullable();
            $table->unsignedInteger('jml_mhs_ts')->nullable();
            $table->unsignedInteger('jml_pertemuan_ts2')->nullable();
            $table->unsignedInteger('jml_pertemuan_ts1')->nullable();
            $table->unsignedInteger('jml_pertemuan_ts')->nullable();
            $table->decimal('lama_bulan', 5, 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_pembimbingan_magang');
    }
};
