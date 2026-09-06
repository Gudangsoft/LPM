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
        Schema::create('dkps_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodi')->cascadeOnDelete();
            $table->foreignId('akreditasi_id')->nullable()->constrained('akreditasi')->nullOnDelete();
            $table->unsignedSmallInteger('tahun_ts_awal');
            $table->unsignedSmallInteger('tahun_ts_akhir');
            $table->string('nama_pengusul')->nullable();
            $table->date('tanggal_pengusulan')->nullable();
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_submissions');
    }
};
