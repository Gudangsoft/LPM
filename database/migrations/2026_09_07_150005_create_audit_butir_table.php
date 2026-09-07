<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_butir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ami_id')->constrained('jadwal_ami')->cascadeOnDelete();
            $table->foreignId('butir_instrumen_id')->constrained('butir_instrumen')->cascadeOnDelete();

            // Diisi auditee (evaluasi diri)
            $table->decimal('nilai_mandiri', 5, 2)->nullable();
            $table->text('deskripsi_capaian')->nullable();

            // Diisi auditor (lembar kerja audit)
            $table->decimal('nilai_auditor', 5, 2)->nullable();
            $table->text('catatan_auditor')->nullable();
            $table->enum('status_verifikasi', ['belum', 'sesuai', 'perlu_perbaikan', 'tidak_sesuai'])->default('belum');

            $table->timestamp('diisi_auditee_at')->nullable();
            $table->timestamp('diisi_auditor_at')->nullable();
            $table->timestamps();

            $table->unique(['jadwal_ami_id', 'butir_instrumen_id'], 'audit_butir_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_butir');
    }
};
