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
        Schema::create('temuan_ami', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ami_id')->constrained('jadwal_ami')->onDelete('cascade');
            $table->foreignId('auditor_id')->constrained('auditor')->onDelete('cascade');
            $table->string('standar'); // Standar SPMI yang diaudit
            $table->enum('kategori', ['mayor', 'minor', 'observasi', 'rekomendasi'])->default('minor');
            $table->text('deskripsi');
            $table->text('bukti')->nullable();
            $table->text('akar_masalah')->nullable();
            $table->text('rekomendasi');
            $table->date('batas_tindak_lanjut')->nullable();
            $table->enum('status', ['open', 'in_progress', 'closed', 'verified'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temuan_ami');
    }
};
