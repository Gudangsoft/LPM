<?php

use App\Models\Prodi;
use App\Models\RpsReview;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Data contoh agar halaman Review RPS tidak kosong saat pertama kali dibuka.
     * Aman dijalankan berulang: dilewati jika tabel sudah berisi data.
     */
    public function up(): void
    {
        if (RpsReview::count() > 0) {
            return;
        }

        $prodiIds = Prodi::pluck('id', 'nama');
        if ($prodiIds->isEmpty()) {
            return;
        }

        $admin = User::where('email', 'like', '%admin%')->value('id') ?? User::query()->value('id');

        $rows = [
            ['prodi' => 'Teknik Informatika', 'kode_mk' => 'TI-301', 'mata_kuliah' => 'Pemrograman Web Lanjut', 'dosen' => 'Dr. Ahmad Fauzi, S.Kom., M.T.', 'sks' => 3, 'semester' => 'ganjil', 'ta' => '2025/2026', 'status' => 'sesuai', 'catatan' => 'CPMK, rencana asesmen, dan referensi sudah lengkap dan sesuai format terbaru.'],
            ['prodi' => 'Teknik Informatika', 'kode_mk' => 'TI-205', 'mata_kuliah' => 'Struktur Data', 'dosen' => 'Rina Kartika, S.Kom., M.Cs.', 'sks' => 3, 'semester' => 'ganjil', 'ta' => '2025/2026', 'status' => 'perlu_revisi', 'catatan' => 'Rencana asesmen belum mencantumkan bobot tiap CPMK, mohon dilengkapi.'],
            ['prodi' => 'Teknik Informatika', 'kode_mk' => 'TI-410', 'mata_kuliah' => 'Kecerdasan Buatan', 'dosen' => 'Dr. Bagus Santoso, M.Kom.', 'sks' => 3, 'semester' => 'genap', 'ta' => '2024/2025', 'status' => 'sesuai', 'catatan' => 'Sudah sesuai, referensi mutakhir.'],
            ['prodi' => 'Teknik Informatika', 'kode_mk' => 'TI-102', 'mata_kuliah' => 'Algoritma dan Pemrograman', 'dosen' => 'Yuni Prasetyo, S.T., M.T.', 'sks' => 4, 'semester' => 'ganjil', 'ta' => '2025/2026', 'status' => 'belum_direview', 'catatan' => null],
            ['prodi' => 'Sistem Informasi', 'kode_mk' => 'SI-301', 'mata_kuliah' => 'Manajemen Proyek Sistem Informasi', 'dosen' => 'Dewi Anggraini, S.Kom., M.M.', 'sks' => 3, 'semester' => 'ganjil', 'ta' => '2025/2026', 'status' => 'sesuai', 'catatan' => 'Lengkap, sesuai capaian pembelajaran lulusan.'],
            ['prodi' => 'Sistem Informasi', 'kode_mk' => 'SI-207', 'mata_kuliah' => 'Basis Data Lanjut', 'dosen' => 'Hendra Wijaya, S.Kom., M.T.', 'sks' => 3, 'semester' => 'genap', 'ta' => '2024/2025', 'status' => 'perlu_revisi', 'catatan' => 'Materi belum mengacu pada RPS payung program studi, mohon disesuaikan.'],
            ['prodi' => 'Sistem Informasi', 'kode_mk' => 'SI-415', 'mata_kuliah' => 'Tata Kelola Teknologi Informasi', 'dosen' => 'Dr. Fitriani, M.M.', 'sks' => 2, 'semester' => 'genap', 'ta' => '2024/2025', 'status' => 'belum_direview', 'catatan' => null],
        ];

        foreach ($rows as $r) {
            $prodiId = $prodiIds[$r['prodi']] ?? $prodiIds->first();

            RpsReview::create([
                'prodi_id' => $prodiId,
                'kode_mk' => $r['kode_mk'],
                'mata_kuliah' => $r['mata_kuliah'],
                'dosen_pengampu' => $r['dosen'],
                'sks' => $r['sks'],
                'semester' => $r['semester'],
                'tahun_akademik' => $r['ta'],
                'status' => $r['status'],
                'catatan_reviewer' => $r['catatan'],
                'reviewed_by' => $r['status'] === 'belum_direview' ? null : $admin,
                'reviewed_at' => $r['status'] === 'belum_direview' ? null : now()->subDays(random_int(1, 20)),
                'submitted_by' => $admin,
            ]);
        }
    }

    public function down(): void
    {
        // Data contoh - tidak perlu rollback otomatis.
    }
};
