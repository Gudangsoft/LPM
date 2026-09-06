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
        Schema::create('dkps_prasarana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->string('nama_prasarana');
            $table->string('fungsi')->nullable();
            $table->unsignedInteger('jumlah_unit')->nullable();
            $table->decimal('total_luas_m2', 10, 2)->nullable();
            $table->enum('kualitas', ['sangat_baik', 'baik', 'kurang_baik', 'tidak_baik']);
            $table->enum('kepemilikan', ['milik_sendiri', 'sewa']);
            $table->enum('kondisi', ['terawat', 'tidak_terawat']);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_prasarana');
    }
};
