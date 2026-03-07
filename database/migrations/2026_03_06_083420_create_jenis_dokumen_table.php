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
        Schema::create('jenis_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('icon')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add jenis_dokumen_id to dokumen table
        Schema::table('dokumen', function (Blueprint $table) {
            $table->foreignId('jenis_dokumen_id')->nullable()->after('id')->constrained('jenis_dokumen')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropForeign(['jenis_dokumen_id']);
            $table->dropColumn('jenis_dokumen_id');
        });
        
        Schema::dropIfExists('jenis_dokumen');
    }
};
