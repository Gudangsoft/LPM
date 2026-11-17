<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Tautan Data Dosen -> akun login (dibuat lewat tombol "Buatkan Akun"). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('prodi_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
