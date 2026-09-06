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
        Schema::create('dkps_kepuasan_pengguna_kemampuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dkps_submission_id')->constrained('dkps_submissions')->cascadeOnDelete();
            $table->enum('jenis_kemampuan', [
                'etika', 'keahlian_bidang_ilmu', 'bahasa_asing', 'ti', 'komunikasi',
                'kerjasama_tim', 'pengembangan_diri', 'berfikir_kritis', 'kreatifitas',
            ]);
            $table->decimal('persen_sangat_baik', 5, 2)->nullable();
            $table->decimal('persen_baik', 5, 2)->nullable();
            $table->decimal('persen_cukup', 5, 2)->nullable();
            $table->decimal('persen_kurang', 5, 2)->nullable();
            $table->text('rencana_tindak_lanjut')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkps_kepuasan_pengguna_kemampuan');
    }
};
