<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fitur formulir kontak (publik /kontak dan admin Pesan Kontak) dihapus.
 * Bersihkan item menu yang masih menunjuk ke route tersebut.
 * Tabel `kontak` sengaja tidak di-drop agar data lama tidak hilang permanen.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('menus')
            ->whereIn('route', ['admin.kontak.index', 'kontak.index'])
            ->delete();
    }

    public function down(): void
    {
        // Fitur sudah dihapus; item menu tidak dipulihkan.
    }
};
