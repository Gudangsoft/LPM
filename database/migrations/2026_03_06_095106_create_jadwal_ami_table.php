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
        Schema::create('jadwal_ami', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_ami_id')->constrained('periode_ami')->onDelete('cascade');
            $table->foreignId('prodi_id')->constrained('prodi')->onDelete('cascade');
            $table->date('tanggal_audit');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('tempat')->nullable();
            $table->enum('status', ['terjadwal', 'berlangsung', 'selesai', 'ditunda', 'batal'])->default('terjadwal');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_ami');
    }
};
