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
        Schema::table('temuan_ami', function (Blueprint $table) {
            $table->foreignId('standar_mutu_id')->nullable()->after('standar')->constrained('standar_mutu')->nullOnDelete();
        });

        Schema::table('dokumen', function (Blueprint $table) {
            $table->foreignId('standar_mutu_id')->nullable()->after('jenis_dokumen_id')->constrained('standar_mutu')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temuan_ami', function (Blueprint $table) {
            $table->dropConstrainedForeignId('standar_mutu_id');
        });

        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropConstrainedForeignId('standar_mutu_id');
        });
    }
};
