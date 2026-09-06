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
        Schema::create('dkps_masa_studi_lulusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('tahun_masuk', ['TS-7', 'TS-6', 'TS-5', 'TS-4', 'TS-3']);
            $table->unsignedInteger('jumlah_diterima')->nullable();
            $table->unsignedInteger('lulus_ts7')->nullable();
            $table->unsignedInteger('lulus_ts6')->nullable();
            $table->unsignedInteger('lulus_ts5')->nullable();
            $table->unsignedInteger('lulus_ts4')->nullable();
            $table->unsignedInteger('lulus_ts3')->nullable();
            $table->unsignedInteger('lulus_ts2')->nullable();
            $table->unsignedInteger('lulus_ts1')->nullable();
            $table->unsignedInteger('lulus_ts')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_masa_studi_lulusan');
    }
};
