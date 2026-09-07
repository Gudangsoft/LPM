<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rtm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_ami_id')->nullable()->constrained('periode_ami')->nullOnDelete();
            $table->string('judul');
            $table->date('tanggal')->nullable();
            $table->string('tempat')->nullable();
            $table->string('pemimpin')->nullable();
            $table->string('notulen')->nullable();
            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->text('ringkasan')->nullable();
            $table->timestamps();
        });

        Schema::create('rtm_agenda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rtm_id')->constrained('rtm')->cascadeOnDelete();
            $table->string('topik');
            $table->text('pembahasan')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('rtm_keputusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rtm_id')->constrained('rtm')->cascadeOnDelete();
            $table->text('keputusan');
            $table->text('rekomendasi')->nullable();
            $table->string('pic')->nullable();
            $table->date('target_tanggal')->nullable();
            $table->enum('status', ['belum', 'proses', 'selesai'])->default('belum');
            $table->text('tindak_lanjut')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rtm_keputusan');
        Schema::dropIfExists('rtm_agenda');
        Schema::dropIfExists('rtm');
    }
};
