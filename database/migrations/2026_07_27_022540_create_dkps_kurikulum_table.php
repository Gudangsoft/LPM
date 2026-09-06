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
        Schema::create('dkps_kurikulum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('semester', ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII']);
            $table->string('kode_mk')->nullable();
            $table->string('nama_mk');
            $table->boolean('kompetensi_inti')->default(false);
            $table->decimal('sks_kuliah', 4, 1)->nullable();
            $table->decimal('sks_praktikum', 4, 1)->nullable();
            $table->decimal('sks_praktik_lapangan', 4, 1)->nullable();
            $table->string('tautan_rps')->nullable();
            $table->string('tautan_asesmen_cpl')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_kurikulum');
    }
};
