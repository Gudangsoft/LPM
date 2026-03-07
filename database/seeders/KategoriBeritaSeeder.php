<?php

namespace Database\Seeders;

use App\Models\KategoriBerita;
use Illuminate\Database\Seeder;

class KategoriBeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            ['nama' => 'Berita Umum', 'slug' => 'berita-umum', 'deskripsi' => 'Berita umum seputar LPM'],
            ['nama' => 'Akreditasi', 'slug' => 'akreditasi', 'deskripsi' => 'Informasi akreditasi program studi dan institusi'],
            ['nama' => 'Kegiatan', 'slug' => 'kegiatan', 'deskripsi' => 'Kegiatan dan acara LPM'],
            ['nama' => 'Kerjasama', 'slug' => 'kerjasama', 'deskripsi' => 'Kerjasama dengan pihak eksternal'],
            ['nama' => 'Prestasi', 'slug' => 'prestasi', 'deskripsi' => 'Prestasi dan penghargaan'],
            ['nama' => 'Pengumuman', 'slug' => 'pengumuman', 'deskripsi' => 'Pengumuman resmi dari LPM'],
        ];

        foreach ($kategoris as $kategori) {
            KategoriBerita::create($kategori);
        }
    }
}