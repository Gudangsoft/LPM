<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rps_review', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodi')->cascadeOnDelete();
            $table->string('kode_mk')->nullable();
            $table->string('mata_kuliah');
            $table->string('dosen_pengampu');
            $table->unsignedTinyInteger('sks')->nullable();
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->string('tahun_akademik', 20);
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->enum('status', ['belum_direview', 'sesuai', 'perlu_revisi'])->default('belum_direview');
            $table->text('catatan_reviewer')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['prodi_id', 'tahun_akademik', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rps_review');
    }
};
