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
        Schema::create('penugasan_ami', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ami_id')->constrained('jadwal_ami')->onDelete('cascade');
            $table->foreignId('auditor_id')->constrained('auditor')->onDelete('cascade');
            $table->enum('peran', ['ketua', 'anggota'])->default('anggota');
            $table->enum('status', ['ditugaskan', 'diterima', 'ditolak', 'selesai'])->default('ditugaskan');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasan_ami');
    }
};
