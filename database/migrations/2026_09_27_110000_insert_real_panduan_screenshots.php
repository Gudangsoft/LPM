<?php

use App\Models\Panduan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Menggantikan kotak placeholder "[SS]" di Buku Panduan dengan screenshot
     * SUNGGUHAN dari sistem ini (diambil lewat Playwright terhadap instance
     * lokal, login sebagai akun admin seed bawaan). Berkasnya sudah disalin
     * manual ke storage/app/public/panduan/ sebelum migration ini dijalankan.
     *
     * Beberapa slot tidak punya padanan screenshot (butuh state modal/tab
     * yang tidak praktis diotomatisasi, atau data yang belum ada seperti RTM)
     * - slot itu dibiarkan sebagai placeholder teks untuk diisi manual nanti.
     */
    public function up(): void
    {
        $old = fn (string $desc) => '<div class="my-3 p-3 rounded" style="border:2px dashed #94a3b8;background:#f8fafc;">'
            . '<p class="mb-0 text-muted"><strong>[SS]</strong> — <em>' . $desc . '</em></p></div>';

        $img = fn (string $desc, string $file) => '<figure class="my-3">'
            . '<img src="/storage/panduan/' . $file . '" alt="' . $desc . '">'
            . '<figcaption class="text-muted small mt-1">' . $desc . '</figcaption></figure>';

        // judul => [ [deskripsi persis, nama berkas], ... ]
        $map = [
            'Profil & Keamanan Akun Saya' => [
                ['Halaman Profil - tab Informasi Akun dengan form nama/email/upload foto', 'profile.png'],
            ],
            'Dashboard Utama' => [
                ['Halaman Dashboard - kartu ringkasan dan grafik utama', 'dashboard-utama.png'],
            ],
            'Dashboard AMI' => [
                ['Dashboard AMI - kartu ringkasan periode aktif', 'dashboard-ami.png'],
                ['Dashboard AMI - grafik status temuan dan temuan per standar', 'dashboard-ami.png'],
            ],
            'Instrumen Audit' => [
                ['Form tambah butir instrumen - field kode, pertanyaan, indikator, bobot, target, jenis bukti', 'instrumen-audit-create.png'],
            ],
            'Evaluasi Diri (Auditee)' => [
                ['Halaman Evaluasi Diri - daftar butir instrumen per standar dengan kolom nilai mandiri', 'evaluasi-diri.png'],
                ['Form isi satu butir instrumen - nilai mandiri dan tombol unggah bukti', 'evaluasi-diri-show.png'],
            ],
            'Lembar Kerja Audit (Auditor)' => [
                ['Halaman Lembar Kerja Audit - daftar butir dengan nilai mandiri auditee di samping kolom nilai auditor', 'lembar-kerja-audit.png'],
                ['Tombol validasi bukti pada satu lampiran berkas', 'lembar-audit-show.png'],
            ],
            'Verifikasi RTL' => [
                ['Halaman Verifikasi RTL - daftar RTL menunggu verifikasi dengan tombol Setujui/Tolak', 'verifikasi-rtl.png'],
            ],
            'Monitoring AMI' => [
                ['Halaman Monitoring - kartu status temuan dan RTL', 'monitoring-ami.png'],
                ['Tabel rollup per prodi dan tabel temuan telat', 'monitoring-ami.png'],
            ],
            'Dokumen/Bukti Audit' => [
                ['Halaman Dokumen/Bukti Audit - daftar berkas dengan filter dan tombol unduh', 'bukti-audit.png'],
            ],
            'Rapat Tinjauan Manajemen (RTM)' => [
                ['Form tambah RTM - judul, tanggal, tempat, pemimpin, periode AMI', 'rtm-create.png'],
            ],
            'Analitik Mutu' => [
                ['Halaman Analitik Mutu - grafik tren dan perbandingan periode/unit', 'analitik-mutu.png'],
            ],
            'Audit Trail' => [
                ['Halaman Audit Trail - tabel log dengan filter entitas, pengguna, dan tanggal', 'audit-trail.png'],
            ],
            'Kebijakan, Manual & Formulir SPMI' => [
                ['Halaman Kebijakan SPMI - daftar dokumen dengan jenis "Kebijakan SPMI" sudah terfilter', 'kebijakan-spmi.png'],
            ],
            'Sasaran Mutu' => [
                ['Form tambah sasaran mutu - pilihan standar, prodi (opsional), uraian, target, satuan', 'sasaran-mutu-create.png'],
                ['Daftar sasaran mutu dengan badge status Tercapai/Tidak Tercapai/Belum Dievaluasi', 'sasaran-mutu.png'],
            ],
            'Pengendalian Dokumen' => [
                ['Halaman Pengendalian Dokumen - tabel dengan kolom versi, terakhir ditinjau, siklus tinjau', 'pengendalian-dokumen.png'],
            ],
            'Rencana Peningkatan Mutu' => [
                ['Halaman Rencana Peningkatan Mutu - tabel keputusan lintas RTM dengan filter', 'rencana-peningkatan-mutu.png'],
            ],
            'Benchmarking' => [
                ['Form tambah benchmarking - aspek, institusi pembanding, nilai sendiri vs pembanding', 'benchmarking-create.png'],
            ],
            'Survey Kepuasan' => [
                ['Form tambah hasil survei - jenis responden, jumlah responden, skala, rata-rata skor', 'survey-kepuasan-create.png'],
                ['Daftar survei kepuasan dengan badge skor dan persentase', 'survey-kepuasan.png'],
            ],
            'Monev' => [
                ['Form tambah monev - aspek, tanggal, petugas, hasil, status', 'monev-create.png'],
            ],
            'Evaluasi Pembelajaran' => [
                ['Form tambah mata kuliah untuk evaluasi pembelajaran', 'evaluasi-pembelajaran-create.png'],
            ],
            'Capaian Pembelajaran' => [
                ['Form tambah CPMK - mata kuliah, kode CPMK, target capaian', 'capaian-pembelajaran-create.png'],
                ['Daftar capaian pembelajaran dengan badge status yang dihitung otomatis', 'capaian-pembelajaran.png'],
            ],
            'Review RPS' => [
                ['Form tambah RPS - kode MK, mata kuliah, dosen, semester, unggah berkas', 'rps-review-create.png'],
            ],
            'Aturan AMI' => [
                ['Tab Aturan AMI di halaman Pengaturan', 'aturan-ami.png'],
            ],
            'Laporan AMI' => [
                ['Halaman Laporan AMI - pilihan periode dan tombol unduh laporan', 'laporan-ami.png'],
            ],
            'Berita & Kategori Berita' => [
                ['Form tambah berita - judul, kategori, gambar sampul, editor konten', 'berita-create.png'],
            ],
            'Pengumuman & Agenda' => [
                ['Halaman daftar pengumuman dan form tambah agenda dengan field tanggal', 'pengumuman.png'],
            ],
            'Slider Beranda' => [
                ['Form tambah slide - unggah gambar, judul overlay, tautan tujuan', 'slider-create.png'],
            ],
            'Pesan Masuk (Kontak)' => [
                ['Daftar pesan masuk dengan penanda belum dibaca', 'kontak.png'],
            ],
            'Masuk sebagai Pengguna Lain (Impersonation)' => [
                ['Tombol "Login sebagai" pada daftar pengguna, dan pita penanda mode impersonation di header', 'users.png'],
            ],
            'Editor Menu Website Publik' => [
                ['Halaman Editor Menu Website - struktur pohon menu dengan tombol tambah/edit', 'menu-web.png'],
            ],
            'Pengaturan Situs' => [
                ['Halaman Pengaturan - tab Pengaturan Umum dengan form nama situs dan unggah logo', 'pengaturan-umum.png'],
            ],
        ];

        foreach ($map as $judul => $entries) {
            $panduan = Panduan::where('judul', $judul)->first();
            if (! $panduan) {
                continue;
            }

            $konten = $panduan->konten;
            foreach ($entries as [$desc, $file]) {
                $konten = str_replace($old($desc), $img($desc, $file), $konten);
            }

            if ($konten !== $panduan->konten) {
                $panduan->update(['konten' => $konten]);
            }
        }
    }

    public function down(): void
    {
        // Konten panduan - tidak ada rollback otomatis.
    }
};
