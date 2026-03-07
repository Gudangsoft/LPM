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
        Schema::create('akreditasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodi')->onDelete('cascade');
            $table->string('lembaga'); // BAN-PT, LAM, Internasional
            $table->string('peringkat'); // A, B, C, Unggul, Baik Sekali, dll
            $table->string('nomor_sk');
            $table->date('tanggal_sk');
            $table->date('tanggal_kadaluarsa');
            $table->string('file_sk')->nullable();
            $table->enum('status', ['aktif', 'kadaluarsa', 'proses_perpanjangan'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akreditasi');
    }
};
