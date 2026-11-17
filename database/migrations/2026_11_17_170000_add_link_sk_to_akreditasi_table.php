<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** SK akreditasi bisa berupa file (PDF/gambar) atau tautan (Google Drive, situs LAM/BAN-PT). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akreditasi', function (Blueprint $table) {
            $table->string('link_sk', 1000)->nullable()->after('file_sk');
        });
    }

    public function down(): void
    {
        Schema::table('akreditasi', function (Blueprint $table) {
            $table->dropColumn('link_sk');
        });
    }
};
