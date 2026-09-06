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
        Schema::create('dkps_penelitian_pkm_ringkasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('jenis', ['penelitian', 'pkm']);
            $table->enum('sumber_pembiayaan', ['pt_mandiri', 'lembaga_dalam_negeri', 'lembaga_luar_negeri']);
            $table->unsignedInteger('jumlah_ts2')->nullable();
            $table->unsignedInteger('jumlah_ts1')->nullable();
            $table->unsignedInteger('jumlah_ts')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_penelitian_pkm_ringkasan');
    }
};
