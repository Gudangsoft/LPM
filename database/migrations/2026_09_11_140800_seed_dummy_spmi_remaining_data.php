<?php

use App\Models\Benchmarking;
use App\Models\CapaianPembelajaran;
use App\Models\EvaluasiPembelajaran;
use App\Models\Monev;
use App\Models\Prodi;
use App\Models\SasaranMutu;
use App\Models\SurveyKepuasan;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Contoh data agar keenam menu SPMI yang baru difungsikan tidak kosong
     * saat pertama kali dibuka. Aman dijalankan berulang: tiap tabel dilewati
     * kalau sudah ada isinya.
     */
    public function up(): void
    {
        $prodiIds = Prodi::pluck('id', 'nama');
        if ($prodiIds->isEmpty()) {
            return;
        }
        $ti = $prodiIds['Teknik Informatika'] ?? $prodiIds->first();
        $si = $prodiIds['Sistem Informasi'] ?? $prodiIds->first();
        $admin = User::where('email', 'like', '%admin%')->value('id') ?? User::query()->value('id');

        $this->seedSasaranMutu($ti, $si);
        $this->seedBenchmarking($ti, $si);
        $this->seedSurveyKepuasan($ti, $si);
        $this->seedMonev($ti, $si);
        $this->seedEvaluasiPembelajaran($ti, $si, $admin);
        $this->seedCapaianPembelajaran($ti, $si);
    }

    private function seedSasaranMutu(int $ti, int $si): void
    {
        if (SasaranMutu::count() > 0) {
            return;
        }

        foreach ([
            ['standar_mutu_id' => 3, 'prodi_id' => $ti, 'uraian_sasaran' => 'Rata-rata IPK lulusan meningkat', 'indikator' => 'IPK rata-rata lulusan', 'target' => '3.30', 'satuan' => 'poin', 'realisasi' => '3.41', 'status' => 'tercapai', 'keterangan' => 'Melebihi target tahun sebelumnya.'],
            ['standar_mutu_id' => 4, 'prodi_id' => null, 'uraian_sasaran' => 'Persentase dosen bersertifikat pendidik', 'indikator' => 'Dosen tersertifikasi', 'target' => '75', 'satuan' => '%', 'realisasi' => '68', 'status' => 'tidak_tercapai', 'keterangan' => 'Masih ada 4 dosen dalam proses sertifikasi.'],
            ['standar_mutu_id' => 6, 'prodi_id' => $si, 'uraian_sasaran' => 'Rasio keketatan mahasiswa baru', 'indikator' => 'Rasio pendaftar:diterima', 'target' => '3:1', 'satuan' => null, 'realisasi' => null, 'status' => 'belum_dievaluasi', 'keterangan' => null],
            ['standar_mutu_id' => 9, 'prodi_id' => $ti, 'uraian_sasaran' => 'Publikasi ilmiah dosen per tahun', 'indikator' => 'Jumlah publikasi terindeks', 'target' => '10', 'satuan' => 'judul', 'realisasi' => '12', 'status' => 'tercapai', 'keterangan' => null],
        ] as $row) {
            SasaranMutu::create($row + ['tahun_akademik' => '2025/2026']);
        }
    }

    private function seedBenchmarking(int $ti, int $si): void
    {
        if (Benchmarking::count() > 0) {
            return;
        }

        foreach ([
            ['standar_mutu_id' => 4, 'prodi_id' => $ti, 'aspek' => 'Rasio dosen:mahasiswa', 'institusi_pembanding' => 'Universitas Mitra A', 'nilai_sendiri' => '1:28', 'nilai_pembanding' => '1:22', 'kesimpulan' => 'Rasio kami masih lebih tinggi dari pembanding.', 'rekomendasi' => 'Rekrutasi dosen tetap tambahan pada bidang RPL.'],
            ['standar_mutu_id' => 9, 'prodi_id' => $si, 'aspek' => 'Masa tunggu kerja lulusan', 'institusi_pembanding' => 'Universitas Mitra B', 'nilai_sendiri' => '4.2 bulan', 'nilai_pembanding' => '3.5 bulan', 'kesimpulan' => 'Sedikit lebih lama dari pembanding.', 'rekomendasi' => 'Perkuat kerja sama penyaluran kerja dengan mitra industri.'],
            ['standar_mutu_id' => null, 'prodi_id' => null, 'aspek' => 'Jumlah kerja sama institusi (MoU aktif)', 'institusi_pembanding' => 'Rata-rata PTS Kaltim', 'nilai_sendiri' => '18', 'nilai_pembanding' => '25', 'kesimpulan' => 'Jumlah MoU aktif masih di bawah rata-rata.', 'rekomendasi' => 'Tingkatkan kerja sama dengan pemerintah daerah dan industri lokal.'],
        ] as $row) {
            Benchmarking::create($row + ['tahun_akademik' => '2025/2026']);
        }
    }

    private function seedSurveyKepuasan(int $ti, int $si): void
    {
        if (SurveyKepuasan::count() > 0) {
            return;
        }

        foreach ([
            ['prodi_id' => null, 'jenis_responden' => 'mahasiswa', 'judul_survei' => 'Survei Kepuasan Layanan Akademik', 'semester' => 'ganjil', 'jumlah_responden' => 214, 'rata_rata_skor' => 3.32, 'skala_maksimal' => 4, 'ringkasan_hasil' => 'Mayoritas mahasiswa puas dengan layanan akademik, keluhan utama pada kecepatan respons administrasi.'],
            ['prodi_id' => $ti, 'jenis_responden' => 'mahasiswa', 'judul_survei' => 'Survei Kepuasan Proses Pembelajaran', 'semester' => 'ganjil', 'jumlah_responden' => 96, 'rata_rata_skor' => 3.45, 'skala_maksimal' => 4, 'ringkasan_hasil' => 'Kepuasan tinggi terhadap materi perkuliahan, masukan pada ketersediaan lab praktikum.'],
            ['prodi_id' => null, 'jenis_responden' => 'pengguna_lulusan', 'judul_survei' => 'Survei Kepuasan Pengguna Lulusan', 'semester' => null, 'jumlah_responden' => 32, 'rata_rata_skor' => 78.5, 'skala_maksimal' => 100, 'ringkasan_hasil' => 'Pengguna lulusan menilai etos kerja baik, kompetensi teknis perlu ditingkatkan.'],
            ['prodi_id' => $si, 'jenis_responden' => 'dosen', 'judul_survei' => 'Survei Kepuasan Dosen terhadap Sarana Prasarana', 'semester' => 'genap', 'jumlah_responden' => 14, 'rata_rata_skor' => null, 'skala_maksimal' => 4, 'ringkasan_hasil' => 'Survei sedang berjalan, hasil belum direkap.'],
        ] as $row) {
            SurveyKepuasan::create($row + ['tahun_akademik' => '2025/2026']);
        }
    }

    private function seedMonev(int $ti, int $si): void
    {
        if (Monev::count() > 0) {
            return;
        }

        foreach ([
            ['prodi_id' => $ti, 'aspek_monev' => 'Kehadiran Dosen', 'hasil' => 'Rata-rata kehadiran dosen 94% dari 16 pertemuan terjadwal.', 'tanggal_monev' => '2025-11-10', 'petugas_monev' => 'Tim LPM', 'status' => 'baik', 'tindak_lanjut' => null],
            ['prodi_id' => $si, 'aspek_monev' => 'Kesesuaian Pelaksanaan dengan RPS', 'hasil' => 'Ditemukan 2 mata kuliah dengan capaian materi di bawah 80% dari rencana RPS.', 'tanggal_monev' => '2025-11-12', 'petugas_monev' => 'Tim LPM', 'status' => 'cukup', 'tindak_lanjut' => 'Koordinasi dengan dosen pengampu untuk menyesuaikan rencana sisa semester.'],
            ['prodi_id' => null, 'aspek_monev' => 'Ketersediaan Sarana Kelas', 'hasil' => 'Proyektor di 2 ruang kelas rusak dan belum diperbaiki lebih dari 1 bulan.', 'tanggal_monev' => '2025-11-15', 'petugas_monev' => 'Tim Sarana', 'status' => 'kurang', 'tindak_lanjut' => 'Pengajuan perbaikan ke bagian umum, target selesai sebelum UAS.'],
        ] as $row) {
            Monev::create($row + ['tahun_akademik' => '2025/2026', 'semester' => 'ganjil']);
        }
    }

    private function seedEvaluasiPembelajaran(int $ti, int $si, int $admin): void
    {
        if (EvaluasiPembelajaran::count() > 0) {
            return;
        }

        foreach ([
            ['prodi_id' => $ti, 'mata_kuliah' => 'Pemrograman Web Lanjut', 'dosen_pengampu' => 'Dr. Ahmad Fauzi, S.Kom., M.T.', 'kesesuaian_rps' => 'sesuai', 'kendala' => null, 'rekomendasi' => null, 'status' => 'dievaluasi', 'dievaluasi_oleh' => $admin, 'dievaluasi_pada' => now()->subDays(5)],
            ['prodi_id' => $ti, 'mata_kuliah' => 'Struktur Data', 'dosen_pengampu' => 'Rina Kartika, S.Kom., M.Cs.', 'kesesuaian_rps' => 'kurang_sesuai', 'kendala' => 'Dua pertemuan tertunda karena dosen dinas luar.', 'rekomendasi' => 'Jadwalkan kelas pengganti sebelum UTS.', 'status' => 'dievaluasi', 'dievaluasi_oleh' => $admin, 'dievaluasi_pada' => now()->subDays(3)],
            ['prodi_id' => $si, 'mata_kuliah' => 'Manajemen Proyek Sistem Informasi', 'dosen_pengampu' => 'Dewi Anggraini, S.Kom., M.M.', 'kesesuaian_rps' => null, 'kendala' => null, 'rekomendasi' => null, 'status' => 'belum_dievaluasi', 'dievaluasi_oleh' => null, 'dievaluasi_pada' => null],
            ['prodi_id' => $si, 'mata_kuliah' => 'Basis Data Lanjut', 'dosen_pengampu' => 'Hendra Wijaya, S.Kom., M.T.', 'kesesuaian_rps' => null, 'kendala' => null, 'rekomendasi' => null, 'status' => 'belum_dievaluasi', 'dievaluasi_oleh' => null, 'dievaluasi_pada' => null],
        ] as $row) {
            EvaluasiPembelajaran::create($row + ['tahun_akademik' => '2025/2026', 'semester' => 'ganjil']);
        }
    }

    private function seedCapaianPembelajaran(int $ti, int $si): void
    {
        if (CapaianPembelajaran::count() > 0) {
            return;
        }

        foreach ([
            ['prodi_id' => $ti, 'mata_kuliah' => 'Pemrograman Web Lanjut', 'cpmk' => 'CPMK-1', 'deskripsi_cpmk' => 'Mahasiswa mampu membangun aplikasi web dengan arsitektur MVC.', 'target_capaian' => 80, 'realisasi_capaian' => 86, 'catatan' => 'Capaian di atas target berdasarkan hasil UAS.'],
            ['prodi_id' => $ti, 'mata_kuliah' => 'Struktur Data', 'cpmk' => 'CPMK-2', 'deskripsi_cpmk' => 'Mahasiswa mampu menerapkan struktur data non-linear.', 'target_capaian' => 75, 'realisasi_capaian' => 68, 'catatan' => 'Perlu penguatan materi pohon dan graf pada semester berikutnya.'],
            ['prodi_id' => $si, 'mata_kuliah' => 'Manajemen Proyek Sistem Informasi', 'cpmk' => 'CPMK-1', 'deskripsi_cpmk' => 'Mahasiswa mampu menyusun rencana proyek TI menggunakan metodologi Agile.', 'target_capaian' => 80, 'realisasi_capaian' => null, 'catatan' => 'Penilaian akhir semester belum selesai direkap.'],
            ['prodi_id' => $si, 'mata_kuliah' => 'Basis Data Lanjut', 'cpmk' => 'CPMK-3', 'deskripsi_cpmk' => 'Mahasiswa mampu merancang basis data terdistribusi.', 'target_capaian' => 75, 'realisasi_capaian' => 79, 'catatan' => null],
        ] as $row) {
            CapaianPembelajaran::create($row + ['tahun_akademik' => '2025/2026', 'semester' => 'ganjil', 'status' => $row['realisasi_capaian'] === null ? 'belum_dievaluasi' : ($row['realisasi_capaian'] >= $row['target_capaian'] ? 'tercapai' : 'tidak_tercapai')]);
        }
    }

    public function down(): void
    {
        // Data contoh - tidak perlu rollback otomatis.
    }
};
