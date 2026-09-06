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
        Schema::create('dkps_penggunaan_dana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('kategori', [
                'biaya_operasional_pendidikan', 'operasional_penelitian', 'operasional_pkm',
                'investasi_sdm', 'investasi_sarana', 'investasi_prasarana',
            ]);
            $table->string('sub_item', 5)->nullable();
            $table->string('jenis_penggunaan')->nullable();
            $table->decimal('up_ps_ts2', 15, 2)->nullable();
            $table->decimal('up_ps_ts1', 15, 2)->nullable();
            $table->decimal('up_ps_ts', 15, 2)->nullable();
            $table->decimal('ps_ts2', 15, 2)->nullable();
            $table->decimal('ps_ts1', 15, 2)->nullable();
            $table->decimal('ps_ts', 15, 2)->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_penggunaan_dana');
    }
};
