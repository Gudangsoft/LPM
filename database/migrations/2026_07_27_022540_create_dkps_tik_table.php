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
        Schema::create('dkps_tik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->string('nama_infrastruktur');
            $table->string('deskripsi')->nullable();
            $table->unsignedInteger('jumlah')->nullable();
            $table->enum('terintegrasi', ['penuh', 'sebagian', 'tidak_terintegrasi']);
            $table->enum('mutahir', ['mutahir', 'tidak_mutahir']);
            $table->boolean('ada_panduan')->default(false);
            $table->enum('kepemilikan', ['milik_sendiri', 'sewa']);
            $table->enum('kondisi', ['terawat', 'tidak_terawat']);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_tik');
    }
};
