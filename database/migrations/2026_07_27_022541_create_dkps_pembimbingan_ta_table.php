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
        Schema::create('dkps_pembimbingan_ta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->foreignId('dosen_tetap_id')->constrained('dosen_tetap')->cascadeOnDelete();
            $table->unsignedInteger('jml_bimbing_ps_sendiri_ts2')->nullable();
            $table->unsignedInteger('jml_bimbing_ps_sendiri_ts1')->nullable();
            $table->unsignedInteger('jml_bimbing_ps_sendiri_ts')->nullable();
            $table->unsignedInteger('jml_bimbing_ps_lain_ts2')->nullable();
            $table->unsignedInteger('jml_bimbing_ps_lain_ts1')->nullable();
            $table->unsignedInteger('jml_bimbing_ps_lain_ts')->nullable();
            $table->unsignedInteger('jml_pertemuan_ts2')->nullable();
            $table->unsignedInteger('jml_pertemuan_ts1')->nullable();
            $table->unsignedInteger('jml_pertemuan_ts')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_pembimbingan_ta');
    }
};
