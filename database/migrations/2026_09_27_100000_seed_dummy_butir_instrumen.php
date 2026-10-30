<?php

use App\Models\ButirInstrumen;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * ButirInstrumen belum pernah diisi data contoh sejak fitur ini dibangun
     * (Fase 1) - membuat halaman Instrumen Audit, Evaluasi Diri, dan Lembar
     * Kerja Audit tampak kosong. Seed beberapa butir nyata di Standar 3
     * (Mahasiswa) dan Standar 6 (Pendidikan) supaya representatif.
     */
    public function up(): void
    {
        if (ButirInstrumen::count() > 0) {
            return;
        }

        foreach ([
            ['standar_mutu_id' => 3, 'kode' => '3.1', 'pertanyaan' => 'Apakah rasio jumlah pendaftar terhadap daya tampung sudah sesuai target?', 'indikator' => 'Rasio keketatan calon mahasiswa baru', 'bobot' => 1.5, 'target' => '≥ 3 : 1', 'jenis_bukti' => 'Data PMB'],
            ['standar_mutu_id' => 3, 'kode' => '3.2', 'pertanyaan' => 'Apakah layanan bimbingan akademik dan konseling berjalan sesuai jadwal?', 'indikator' => 'Frekuensi bimbingan per semester', 'bobot' => 1, 'target' => '≥ 4 kali/semester', 'jenis_bukti' => 'Log bimbingan'],
            ['standar_mutu_id' => 3, 'kode' => '3.3', 'pertanyaan' => 'Apakah keluhan mahasiswa ditindaklanjuti dalam batas waktu yang ditetapkan?', 'indikator' => 'Persentase keluhan tertangani', 'bobot' => 1, 'target' => '100%', 'jenis_bukti' => 'Rekap pengaduan'],
            ['standar_mutu_id' => 6, 'kode' => '6.1', 'pertanyaan' => 'Apakah RPS tiap mata kuliah tersedia dan diperbarui setiap tahun akademik?', 'indikator' => 'Kelengkapan RPS', 'bobot' => 2, 'target' => '100% mata kuliah', 'jenis_bukti' => 'Berkas RPS'],
            ['standar_mutu_id' => 6, 'kode' => '6.2', 'pertanyaan' => 'Apakah pelaksanaan perkuliahan sesuai dengan RPS yang direncanakan?', 'indikator' => 'Kesesuaian pelaksanaan-RPS', 'bobot' => 1.5, 'target' => '≥ 90%', 'jenis_bukti' => 'Berita acara perkuliahan'],
            ['standar_mutu_id' => 6, 'kode' => '6.3', 'pertanyaan' => 'Apakah hasil penilaian pembelajaran didokumentasikan secara lengkap?', 'indikator' => 'Kelengkapan dokumen penilaian', 'bobot' => 1, 'target' => '100%', 'jenis_bukti' => 'Rekap nilai'],
        ] as $i => $row) {
            ButirInstrumen::create($row + ['urutan' => $i + 1, 'is_active' => true]);
        }
    }

    public function down(): void
    {
        // Data contoh - tidak perlu rollback otomatis.
    }
};
