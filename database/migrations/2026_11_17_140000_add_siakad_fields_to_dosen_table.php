<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lengkapi Data Dosen dengan kolom yang ada di SIAKAD (kode dosen, jenis kelamin,
 * kontak, alamat, pendidikan terakhir & asal PT, golongan) agar mudah diimpor.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->string('kode_dosen', 30)->nullable()->after('prodi_id');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('nama');
            $table->string('no_hp', 30)->nullable()->after('email');
            $table->text('alamat')->nullable()->after('no_hp');
            $table->string('pendidikan_terakhir', 10)->nullable()->after('alamat');
            $table->string('lulusan')->nullable()->after('pendidikan_terakhir');
            $table->string('golongan', 30)->nullable()->after('bidang_keahlian');

            $table->index('kode_dosen');
        });
    }

    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->dropIndex(['kode_dosen']);
            $table->dropColumn(['kode_dosen', 'jenis_kelamin', 'no_hp', 'alamat', 'pendidikan_terakhir', 'lulusan', 'golongan']);
        });
    }
};
