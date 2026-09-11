<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monev', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->nullable()->constrained('prodi')->cascadeOnDelete();
            $table->string('tahun_akademik', 20);
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->string('aspek_monev');
            $table->text('hasil');
            $table->date('tanggal_monev');
            $table->string('petugas_monev')->nullable();
            $table->enum('status', ['baik', 'cukup', 'kurang'])->default('baik');
            $table->text('tindak_lanjut')->nullable();
            $table->timestamps();

            $table->index(['prodi_id', 'tahun_akademik', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monev');
    }
};
