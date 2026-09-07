<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('butir_instrumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standar_mutu_id')->constrained('standar_mutu')->cascadeOnDelete();
            $table->string('kode')->nullable();               // 1.1, 2.3, ...
            $table->text('pertanyaan');
            $table->text('indikator')->nullable();
            $table->decimal('bobot', 5, 2)->default(1);
            $table->string('target')->nullable();             // ">= 3.5", "100%"
            $table->string('jenis_bukti')->nullable();        // dokumen / wawancara / observasi
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['standar_mutu_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('butir_instrumen');
    }
};
