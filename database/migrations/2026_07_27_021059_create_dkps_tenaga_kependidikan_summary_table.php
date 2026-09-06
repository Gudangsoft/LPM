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
        Schema::create('dkps_tenaga_kependidikan_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('jenis', ['pustakawan', 'laboran', 'administrasi', 'lainnya']);
            $table->unsignedInteger('jumlah_s3')->default(0);
            $table->unsignedInteger('jumlah_s2')->default(0);
            $table->unsignedInteger('jumlah_s1')->default(0);
            $table->unsignedInteger('jumlah_d4')->default(0);
            $table->unsignedInteger('jumlah_d3')->default(0);
            $table->unsignedInteger('jumlah_sma_smk')->default(0);
            $table->string('unit_kerja')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_tenaga_kependidikan_summary');
    }
};
