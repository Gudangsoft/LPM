<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Kolom tambahan dari ekspor SIAKAD: NIP, tempat & tanggal lahir, agama. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->string('nip', 30)->nullable()->after('nidn_nidk');
            $table->string('tempat_lahir', 100)->nullable()->after('jenis_kelamin');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('agama', 30)->nullable()->after('tanggal_lahir');
        });
    }

    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->dropColumn(['nip', 'tempat_lahir', 'tanggal_lahir', 'agama']);
        });
    }
};
