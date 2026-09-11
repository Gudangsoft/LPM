<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benchmarking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standar_mutu_id')->nullable()->constrained('standar_mutu')->nullOnDelete();
            $table->foreignId('prodi_id')->nullable()->constrained('prodi')->cascadeOnDelete();
            $table->string('tahun_akademik', 20);
            $table->string('aspek');
            $table->string('institusi_pembanding');
            $table->string('nilai_sendiri')->nullable();
            $table->string('nilai_pembanding')->nullable();
            $table->text('kesimpulan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benchmarking');
    }
};
