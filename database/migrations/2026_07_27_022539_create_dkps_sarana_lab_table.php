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
        Schema::create('dkps_sarana_lab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->string('nama_lab_ruang');
            $table->string('nama_alat_peraga');
            $table->enum('kualitas', ['sangat_baik', 'baik', 'kurang_baik', 'tidak_baik']);
            $table->unsignedInteger('jumlah')->nullable();
            $table->enum('kepemilikan', ['milik_sendiri', 'sewa']);
            $table->enum('kondisi', ['terawat', 'tidak_terawat']);
            $table->decimal('rata_rata_jam_minggu', 6, 2)->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_sarana_lab');
    }
};
