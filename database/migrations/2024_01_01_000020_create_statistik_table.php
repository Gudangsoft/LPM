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
        Schema::create('statistik', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // penelitian, pengabdian, publikasi, etc
            $table->year('tahun');
            $table->integer('usulan')->default(0);
            $table->integer('didanai')->default(0);
            $table->decimal('dana_usulan', 15, 2)->default(0);
            $table->decimal('dana_disetujui', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['kategori', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistik');
    }
};
