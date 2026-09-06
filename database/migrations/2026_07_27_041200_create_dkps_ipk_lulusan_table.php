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
        Schema::create('dkps_ipk_lulusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('tahun_lulus', ['TS-2', 'TS-1', 'TS']);
            $table->unsignedInteger('jumlah_lulusan')->nullable();
            $table->decimal('ipk_min', 4, 2)->nullable();
            $table->decimal('ipk_rata', 4, 2)->nullable();
            $table->decimal('ipk_maks', 4, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_ipk_lulusan');
    }
};
