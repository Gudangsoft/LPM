<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_audit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_butir_id')->constrained('audit_butir')->cascadeOnDelete();
            $table->string('judul');
            $table->string('file_path')->nullable();
            $table->string('tautan')->nullable();
            $table->unsignedInteger('versi')->default(1);
            $table->text('keterangan')->nullable();
            $table->enum('status_validasi', ['belum', 'valid', 'tidak_valid'])->default('belum');
            $table->text('catatan_validasi')->nullable();
            $table->foreignId('diunggah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('audit_butir_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_audit');
    }
};
