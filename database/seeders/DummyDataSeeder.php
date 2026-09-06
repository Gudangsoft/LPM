<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\Dokumen;
use App\Models\Galeri;
use App\Models\JenisDokumen;
use App\Models\KategoriBerita;
use App\Models\Kontak;
use App\Models\Pengumuman;
use App\Models\Slider;
use App\Models\StrukturOrganisasi;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding dummy data...');

        // Seed Kategori Berita
        $this->seedKategoriBerita();

        // Seed Jenis Dokumen
        $this->seedJenisDokumen();

        // Seed Sliders
        $this->seedSliders();

        // Seed Berita
        $this->seedBerita();

        // Seed Pengumuman
        $this->seedPengumuman();

        // Seed Agenda
        $this->seedAgenda();

        // Seed Galeri
        $this->seedGaleri();

        // Seed Dokumen
        $this->seedDokumen();

        // Seed Struktur Organisasi
        $this->seedStrukturOrganisasi();

        // Seed Kontak
        $this->seedKontak();

        // Seed Visitors
        $this->seedVisitors();

        $this->command->info('Dummy data seeded successfully!');
    }

    private function seedKategoriBerita(): void
    {
        $categories = [
            ['nama' => 'Audit Mutu Internal', 'slug' => 'audit-mutu-internal', 'deskripsi' => 'Berita terkait pelaksanaan Audit Mutu Internal'],
            ['nama' => 'Akreditasi', 'slug' => 'akreditasi', 'deskripsi' => 'Informasi terkait proses akreditasi'],
            ['nama' => 'Pelatihan', 'slug' => 'pelatihan', 'deskripsi' => 'Kegiatan pelatihan dan workshop'],
            ['nama' => 'Sosialisasi', 'slug' => 'sosialisasi', 'deskripsi' => 'Kegiatan sosialisasi SPMI'],
            ['nama' => 'Kerjasama', 'slug' => 'kerjasama', 'deskripsi' => 'Kerjasama dengan institusi lain'],
        ];

        foreach ($categories as $cat) {
            KategoriBerita::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }

    private function seedJenisDokumen(): void
    {
        $types = [
            ['nama' => 'Kebijakan SPMI', 'slug' => 'kebijakan-spmi', 'icon' => 'bi-file-earmark-text', 'urutan' => 1],
            ['nama' => 'Standar SPMI', 'slug' => 'standar-spmi', 'icon' => 'bi-file-earmark-check', 'urutan' => 2],
            ['nama' => 'Manual SPMI', 'slug' => 'manual-spmi', 'icon' => 'bi-book', 'urutan' => 3],
            ['nama' => 'Formulir', 'slug' => 'formulir', 'icon' => 'bi-file-earmark-ruled', 'urutan' => 4],
            ['nama' => 'SOP', 'slug' => 'sop', 'icon' => 'bi-diagram-3', 'urutan' => 5],
            ['nama' => 'Laporan', 'slug' => 'laporan', 'icon' => 'bi-file-earmark-bar-graph', 'urutan' => 6],
            ['nama' => 'Panduan', 'slug' => 'panduan', 'icon' => 'bi-journal-text', 'urutan' => 7],
            ['nama' => 'SK & Surat', 'slug' => 'sk-surat', 'icon' => 'bi-envelope-paper', 'urutan' => 8],
        ];

        foreach ($types as $type) {
            JenisDokumen::updateOrCreate(['slug' => $type['slug']], array_merge($type, ['is_active' => true]));
        }
    }

    private function seedSliders(): void
    {
        $sliders = [
            [
                'judul' => 'Selamat Datang di Lembaga Penjaminan Mutu',
                'deskripsi' => 'Menjamin dan meningkatkan mutu pendidikan tinggi melalui sistem penjaminan mutu internal yang berkelanjutan dan terintegrasi.',
                'gambar' => 'sliders/slider-1.jpg',
                'link' => '/tentang-lpm/profil',
                'button_text' => 'Penjaminan Mutu',
                'urutan' => 1,
                'is_active' => true,
            ],
            [
                'judul' => 'Audit Mutu Internal 2026',
                'deskripsi' => 'Pelaksanaan Audit Mutu Internal untuk memastikan standar mutu pendidikan terpenuhi di seluruh unit kerja.',
                'gambar' => 'sliders/slider-2.jpg',
                'link' => '/berita',
                'button_text' => 'AMI 2026',
                'urutan' => 2,
                'is_active' => true,
            ],
            [
                'judul' => 'Akreditasi Unggul',
                'deskripsi' => 'Mendampingi program studi dan institusi dalam mencapai akreditasi unggul dari BAN-PT dan LAM.',
                'gambar' => 'sliders/slider-3.jpg',
                'link' => '/dokumen',
                'button_text' => 'Akreditasi',
                'urutan' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::updateOrCreate(['judul' => $slider['judul']], $slider);
        }
    }

    private function seedBerita(): void
    {
        $user = User::first();
        $categories = KategoriBerita::all();

        $newsData = [
            [
                'judul' => 'Pelaksanaan Audit Mutu Internal Semester Genap 2025/2026',
                'ringkasan' => 'LPM melaksanakan Audit Mutu Internal (AMI) pada seluruh unit kerja untuk memastikan standar mutu terpenuhi.',
                'konten' => '<p>Lembaga Penjaminan Mutu (LPM) telah melaksanakan Audit Mutu Internal (AMI) Semester Genap Tahun Akademik 2025/2026. Kegiatan ini dilaksanakan pada tanggal 1-15 Maret 2026 dengan melibatkan 50 auditor internal yang telah bersertifikasi.</p>
                <p>AMI merupakan kegiatan rutin yang dilakukan setiap semester untuk memastikan bahwa standar mutu pendidikan di seluruh unit kerja terpenuhi. Auditor akan memeriksa kesesuaian dokumen, implementasi standar, dan efektivitas sistem penjaminan mutu internal.</p>
                <h4>Tujuan AMI</h4>
                <ul>
                <li>Memastikan tercapainya standar SPMI</li>
                <li>Mengidentifikasi area yang perlu perbaikan</li>
                <li>Memberikan rekomendasi peningkatan mutu</li>
                <li>Mendukung persiapan akreditasi</li>
                </ul>
                <p>Hasil AMI akan digunakan sebagai dasar untuk penyusunan rencana tindak lanjut dan perbaikan berkelanjutan.</p>',
                'kategori_slug' => 'audit-mutu-internal',
                'is_published' => true,
            ],
            [
                'judul' => 'Workshop Penyusunan Borang Akreditasi Program Studi',
                'ringkasan' => 'LPM mengadakan workshop untuk membantu program studi dalam menyusun borang akreditasi sesuai standar BAN-PT.',
                'konten' => '<p>Dalam rangka persiapan akreditasi program studi, LPM mengadakan Workshop Penyusunan Borang Akreditasi pada tanggal 20-21 Februari 2026. Workshop ini diikuti oleh perwakilan dari 25 program studi.</p>
                <p>Narasumber dalam workshop ini adalah para asesor BAN-PT yang berpengalaman dalam mengevaluasi borang akreditasi. Peserta mendapatkan pemahaman mendalam tentang kriteria penilaian dan teknik penulisan borang yang efektif.</p>
                <h4>Materi Workshop</h4>
                <ol>
                <li>Pemahaman Instrumen Akreditasi 4.0</li>
                <li>Teknik Pengumpulan Data dan Evidensi</li>
                <li>Penulisan Deskripsi yang Efektif</li>
                <li>Simulasi Penilaian Mandiri</li>
                </ol>',
                'kategori_slug' => 'akreditasi',
                'is_published' => true,
            ],
            [
                'judul' => 'Pelatihan Auditor Internal Angkatan ke-10',
                'ringkasan' => 'LPM menyelenggarakan pelatihan auditor internal untuk meningkatkan kapasitas SDM dalam pelaksanaan audit mutu.',
                'konten' => '<p>LPM menyelenggarakan Pelatihan Auditor Internal Angkatan ke-10 pada tanggal 5-7 Januari 2026. Pelatihan ini diikuti oleh 30 peserta dari berbagai unit kerja yang akan menjadi auditor internal.</p>
                <p>Pelatihan dilaksanakan selama 3 hari dengan metode workshop dan praktik langsung. Peserta yang lulus akan menerima sertifikat sebagai Auditor Internal SPMI.</p>
                <h4>Kompetensi yang Dikembangkan</h4>
                <ul>
                <li>Pemahaman sistem penjaminan mutu internal</li>
                <li>Teknik audit dan investigasi</li>
                <li>Komunikasi efektif dalam audit</li>
                <li>Penulisan laporan hasil audit</li>
                </ul>',
                'kategori_slug' => 'pelatihan',
                'is_published' => true,
            ],
            [
                'judul' => 'Sosialisasi SPMI untuk Dosen Baru',
                'ringkasan' => 'LPM mengadakan sosialisasi sistem penjaminan mutu internal bagi dosen baru untuk meningkatkan pemahaman tentang budaya mutu.',
                'konten' => '<p>Sosialisasi Sistem Penjaminan Mutu Internal (SPMI) untuk dosen baru telah dilaksanakan pada tanggal 10 Januari 2026. Kegiatan ini diikuti oleh 45 dosen yang baru bergabung di semester ini.</p>
                <p>Sosialisasi bertujuan untuk memberikan pemahaman dasar tentang SPMI, standar mutu pendidikan, dan peran dosen dalam implementasi budaya mutu di lingkungan kampus.</p>',
                'kategori_slug' => 'sosialisasi',
                'is_published' => true,
            ],
            [
                'judul' => 'MoU Kerjasama dengan LPM Universitas Terkemuka',
                'ringkasan' => 'LPM menjalin kerjasama dengan lembaga penjaminan mutu dari universitas lain untuk berbagi praktik terbaik.',
                'konten' => '<p>LPM telah menandatangani Memorandum of Understanding (MoU) dengan Lembaga Penjaminan Mutu dari 5 universitas terkemuka di Indonesia. Kerjasama ini bertujuan untuk berbagi praktik terbaik dalam implementasi SPMI.</p>
                <p>Ruang lingkup kerjasama meliputi:</p>
                <ul>
                <li>Pertukaran auditor internal</li>
                <li>Sharing session best practices</li>
                <li>Joint workshop dan pelatihan</li>
                <li>Benchmarking sistem mutu</li>
                </ul>',
                'kategori_slug' => 'kerjasama',
                'is_published' => true,
            ],
            [
                'judul' => 'Program Studi Teknik Informatika Raih Akreditasi Unggul',
                'ringkasan' => 'Selamat kepada Program Studi Teknik Informatika yang berhasil meraih akreditasi Unggul dari BAN-PT.',
                'konten' => '<p>Dengan bangga kami mengumumkan bahwa Program Studi Teknik Informatika telah berhasil meraih akreditasi <strong>UNGGUL</strong> dari Badan Akreditasi Nasional Perguruan Tinggi (BAN-PT).</p>
                <p>Pencapaian ini merupakan hasil kerja keras seluruh civitas akademika dan dukungan penuh dari LPM dalam pendampingan penyusunan borang dan persiapan visitasi.</p>
                <p>Dengan akreditasi Unggul, Program Studi Teknik Informatika semakin membuktikan komitmennya dalam memberikan pendidikan berkualitas tinggi kepada mahasiswa.</p>',
                'kategori_slug' => 'akreditasi',
                'is_published' => true,
            ],
        ];

        foreach ($newsData as $index => $news) {
            $kategori = $categories->where('slug', $news['kategori_slug'])->first();
            
            Berita::updateOrCreate(
                ['slug' => Str::slug($news['judul'])],
                [
                    'kategori_id' => $kategori?->id,
                    'user_id' => $user?->id ?? 1,
                    'judul' => $news['judul'],
                    'slug' => Str::slug($news['judul']),
                    'ringkasan' => $news['ringkasan'],
                    'konten' => $news['konten'],
                    'thumbnail' => 'berita/berita-' . ($index + 1) . '.jpg',
                    'views' => rand(50, 500),
                    'is_published' => $news['is_published'],
                    'published_at' => now()->subDays(rand(1, 60)),
                ]
            );
        }
    }

    private function seedPengumuman(): void
    {
        $announcements = [
            [
                'judul' => 'Jadwal Audit Mutu Internal Semester Genap 2025/2026',
                'konten' => '<p>Diberitahukan kepada seluruh unit kerja bahwa pelaksanaan Audit Mutu Internal (AMI) Semester Genap TA 2025/2026 akan dilaksanakan pada:</p>
                <p><strong>Tanggal:</strong> 1 - 15 Maret 2026<br>
                <strong>Waktu:</strong> 08.00 - 16.00 WIB</p>
                <p>Dimohon kepada seluruh unit kerja untuk mempersiapkan dokumen-dokumen yang diperlukan. Jadwal lengkap per unit kerja akan diumumkan kemudian.</p>',
                'is_important' => true,
                'tanggal_mulai' => now()->subDays(5),
                'tanggal_selesai' => now()->addDays(30),
            ],
            [
                'judul' => 'Pelatihan Auditor Internal Angkatan ke-11',
                'konten' => '<p>LPM membuka pendaftaran Pelatihan Auditor Internal Angkatan ke-11. Pelatihan akan dilaksanakan pada:</p>
                <p><strong>Tanggal:</strong> 15 - 17 April 2026<br>
                <strong>Tempat:</strong> Ruang Pelatihan LPM</p>
                <p>Pendaftaran dibuka mulai tanggal 1 - 10 April 2026. Kuota terbatas 30 peserta.</p>',
                'is_important' => true,
                'tanggal_mulai' => now(),
                'tanggal_selesai' => now()->addDays(45),
            ],
            [
                'judul' => 'Pengumpulan Laporan Evaluasi Diri Prodi',
                'konten' => '<p>Seluruh Program Studi dimohon untuk mengumpulkan Laporan Evaluasi Diri (LED) paling lambat tanggal 31 Maret 2026.</p>
                <p>Format LED dapat diunduh melalui menu Dokumen pada website LPM.</p>',
                'is_important' => false,
                'tanggal_mulai' => now()->subDays(10),
                'tanggal_selesai' => now()->addDays(20),
            ],
            [
                'judul' => 'Rapat Koordinasi Tim SPMI',
                'konten' => '<p>Akan diadakan Rapat Koordinasi Tim SPMI pada:<br>
                <strong>Hari/Tanggal:</strong> Senin, 10 Maret 2026<br>
                <strong>Waktu:</strong> 09.00 WIB<br>
                <strong>Tempat:</strong> Ruang Rapat LPM</p>
                <p>Agenda: Evaluasi pelaksanaan AMI dan pembahasan tindak lanjut.</p>',
                'is_important' => false,
                'tanggal_mulai' => now(),
                'tanggal_selesai' => now()->addDays(7),
            ],
        ];

        foreach ($announcements as $ann) {
            Pengumuman::updateOrCreate(
                ['slug' => Str::slug($ann['judul'])],
                array_merge($ann, [
                    'slug' => Str::slug($ann['judul']),
                    'is_active' => true,
                ])
            );
        }
    }

    private function seedAgenda(): void
    {
        $agendas = [
            [
                'judul' => 'Rapat Persiapan AMI Semester Genap',
                'deskripsi' => 'Rapat koordinasi persiapan pelaksanaan Audit Mutu Internal Semester Genap 2025/2026.',
                'lokasi' => 'Ruang Rapat LPM Lt. 2',
                'tanggal_mulai' => now()->addDays(3),
                'tanggal_selesai' => now()->addDays(3),
                'waktu_mulai' => '09:00',
                'waktu_selesai' => '12:00',
            ],
            [
                'judul' => 'Workshop Penyusunan Standar Mutu',
                'deskripsi' => 'Workshop penyusunan dan revisi standar mutu sesuai dengan perkembangan regulasi terbaru.',
                'lokasi' => 'Auditorium Kampus',
                'tanggal_mulai' => now()->addDays(10),
                'tanggal_selesai' => now()->addDays(11),
                'waktu_mulai' => '08:00',
                'waktu_selesai' => '16:00',
            ],
            [
                'judul' => 'Sosialisasi Kebijakan SPMI Terbaru',
                'deskripsi' => 'Sosialisasi perubahan kebijakan SPMI kepada seluruh pimpinan unit kerja.',
                'lokasi' => 'Ruang Sidang Utama',
                'tanggal_mulai' => now()->addDays(15),
                'tanggal_selesai' => now()->addDays(15),
                'waktu_mulai' => '13:00',
                'waktu_selesai' => '16:00',
            ],
            [
                'judul' => 'Pelaksanaan Audit Mutu Internal',
                'deskripsi' => 'Pelaksanaan AMI di seluruh unit kerja dan program studi.',
                'lokasi' => 'Seluruh Unit Kerja',
                'tanggal_mulai' => now()->addDays(20),
                'tanggal_selesai' => now()->addDays(30),
                'waktu_mulai' => '08:00',
                'waktu_selesai' => '16:00',
            ],
            [
                'judul' => 'Rapat Tinjauan Manajemen',
                'deskripsi' => 'Rapat Tinjauan Manajemen (RTM) untuk membahas hasil AMI dan rencana tindak lanjut.',
                'lokasi' => 'Ruang Rapat Rektorat',
                'tanggal_mulai' => now()->addDays(40),
                'tanggal_selesai' => now()->addDays(40),
                'waktu_mulai' => '09:00',
                'waktu_selesai' => '15:00',
            ],
        ];

        foreach ($agendas as $agenda) {
            Agenda::updateOrCreate(
                ['slug' => Str::slug($agenda['judul'])],
                array_merge($agenda, [
                    'slug' => Str::slug($agenda['judul']),
                    'is_active' => true,
                ])
            );
        }
    }

    private function seedGaleri(): void
    {
        $galleries = [
            ['judul' => 'Pelaksanaan AMI 2025', 'deskripsi' => 'Dokumentasi kegiatan Audit Mutu Internal Tahun 2025', 'kategori' => 'Audit'],
            ['judul' => 'Workshop Akreditasi', 'deskripsi' => 'Kegiatan workshop persiapan akreditasi program studi', 'kategori' => 'Workshop'],
            ['judul' => 'Pelatihan Auditor Internal', 'deskripsi' => 'Pelatihan calon auditor internal SPMI', 'kategori' => 'Pelatihan'],
            ['judul' => 'Visitasi BAN-PT', 'deskripsi' => 'Dokumentasi visitasi asesor BAN-PT', 'kategori' => 'Akreditasi'],
            ['judul' => 'Rapat Koordinasi LPM', 'deskripsi' => 'Rapat koordinasi tim penjaminan mutu', 'kategori' => 'Rapat'],
            ['judul' => 'Sosialisasi SPMI', 'deskripsi' => 'Kegiatan sosialisasi sistem penjaminan mutu internal', 'kategori' => 'Sosialisasi'],
            ['judul' => 'Benchmarking LPM', 'deskripsi' => 'Kunjungan benchmarking ke universitas lain', 'kategori' => 'Kerjasama'],
            ['judul' => 'Wisuda Mahasiswa', 'deskripsi' => 'Dokumentasi prosesi wisuda mahasiswa', 'kategori' => 'Kegiatan'],
        ];

        foreach ($galleries as $index => $gallery) {
            Galeri::updateOrCreate(
                ['slug' => Str::slug($gallery['judul'])],
                array_merge($gallery, [
                    'slug' => Str::slug($gallery['judul']),
                    'gambar' => 'galeri/gallery-' . ($index + 1) . '.jpg',
                    'urutan' => $index + 1,
                    'is_active' => true,
                ])
            );
        }
    }

    private function seedDokumen(): void
    {
        $jenisDokumen = JenisDokumen::all()->keyBy('slug');

        $documents = [
            ['judul' => 'Kebijakan SPMI Tahun 2025', 'jenis' => 'kebijakan-spmi', 'kategori' => 'Kebijakan'],
            ['judul' => 'Standar Pendidikan', 'jenis' => 'standar-spmi', 'kategori' => 'Standar'],
            ['judul' => 'Standar Penelitian', 'jenis' => 'standar-spmi', 'kategori' => 'Standar'],
            ['judul' => 'Standar Pengabdian kepada Masyarakat', 'jenis' => 'standar-spmi', 'kategori' => 'Standar'],
            ['judul' => 'Manual SPMI', 'jenis' => 'manual-spmi', 'kategori' => 'Manual'],
            ['judul' => 'Formulir Audit Internal', 'jenis' => 'formulir', 'kategori' => 'Formulir'],
            ['judul' => 'Formulir Evaluasi Diri', 'jenis' => 'formulir', 'kategori' => 'Formulir'],
            ['judul' => 'SOP Audit Mutu Internal', 'jenis' => 'sop', 'kategori' => 'SOP'],
            ['judul' => 'SOP Penyusunan Kurikulum', 'jenis' => 'sop', 'kategori' => 'SOP'],
            ['judul' => 'Laporan AMI Semester Ganjil 2025/2026', 'jenis' => 'laporan', 'kategori' => 'Laporan'],
            ['judul' => 'Panduan Audit Internal', 'jenis' => 'panduan', 'kategori' => 'Panduan'],
            ['judul' => 'Panduan Penyusunan LED', 'jenis' => 'panduan', 'kategori' => 'Panduan'],
            ['judul' => 'SK Tim SPMI 2026', 'jenis' => 'sk-surat', 'kategori' => 'SK'],
            ['judul' => 'SK Auditor Internal', 'jenis' => 'sk-surat', 'kategori' => 'SK'],
        ];

        $disk = Storage::disk('public');

        foreach ($documents as $index => $doc) {
            $jenis = $jenisDokumen->get($doc['jenis']);
            $filePath = 'dokumen/doc-' . ($index + 1) . '.pdf';

            // Make sure a real PDF actually exists on disk so downloads work
            // (also replaces any 0-byte / plain-text placeholder stubs).
            $isUsable = $disk->exists($filePath)
                && str_starts_with((string) $disk->get($filePath), '%PDF-')
                && $disk->size($filePath) > 200;

            if (! $isUsable) {
                $disk->put($filePath, $this->placeholderPdf($doc['judul']));
            }

            Dokumen::updateOrCreate(
                ['slug' => Str::slug($doc['judul'])],
                [
                    'judul' => $doc['judul'],
                    'slug' => Str::slug($doc['judul']),
                    'deskripsi' => 'Dokumen ' . $doc['judul'] . ' untuk sistem penjaminan mutu internal.',
                    'file_path' => $filePath,
                    'file_name' => Str::slug($doc['judul']) . '.pdf',
                    'file_size' => $disk->size($filePath),
                    'file_type' => 'pdf',
                    'kategori' => $doc['kategori'],
                    'jenis_dokumen_id' => $jenis?->id,
                    'download_count' => rand(10, 200),
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Build a minimal but valid single-page PDF used as a placeholder document.
     */
    private function placeholderPdf(string $title): string
    {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $title);
        $stream = "BT /F1 18 Tf 56 760 Td ($text) Tj ET";

        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] '
                . '/Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            5 => '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $num => $body) {
            $offsets[$num] = strlen($pdf);
            $pdf .= $num . " 0 obj\n" . $body . "\nendobj\n";
        }

        $xref = strlen($pdf);
        $size = count($objects) + 1;
        $pdf .= "xref\n0 {$size}\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer\n<< /Size {$size} /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";

        return $pdf;
    }

    private function seedStrukturOrganisasi(): void
    {
        $struktur = [
            [
                'nama' => 'Prof. Dr. Ahmad Hidayat, M.Pd.',
                'nip' => '196501151990031001',
                'jabatan' => 'Ketua LPM',
                'bio' => 'Profesor di bidang Manajemen Pendidikan dengan pengalaman lebih dari 25 tahun dalam penjaminan mutu pendidikan tinggi.',
                'email' => 'ketua.lpm@universitas.ac.id',
                'telepon' => '(021) 1234567 ext. 101',
                'urutan' => 1,
            ],
            [
                'nama' => 'Dr. Siti Rahayu, M.Si.',
                'nip' => '197203201998032001',
                'jabatan' => 'Sekretaris LPM',
                'bio' => 'Doktor di bidang Evaluasi Pendidikan dengan fokus penelitian pada akreditasi pendidikan tinggi.',
                'email' => 'sekretaris.lpm@universitas.ac.id',
                'telepon' => '(021) 1234567 ext. 102',
                'urutan' => 2,
            ],
            [
                'nama' => 'Dr. Bambang Wijaya, M.T.',
                'nip' => '198005102005011001',
                'jabatan' => 'Kepala Bidang Standar Mutu',
                'bio' => 'Ahli dalam pengembangan standar mutu dan sistem manajemen mutu ISO.',
                'email' => 'standar.lpm@universitas.ac.id',
                'telepon' => '(021) 1234567 ext. 103',
                'urutan' => 3,
            ],
            [
                'nama' => 'Dra. Endang Lestari, M.Pd.',
                'nip' => '196808151993032001',
                'jabatan' => 'Kepala Bidang Audit Internal',
                'bio' => 'Berpengalaman sebagai lead auditor dengan sertifikasi ISO 9001:2015.',
                'email' => 'audit.lpm@universitas.ac.id',
                'telepon' => '(021) 1234567 ext. 104',
                'urutan' => 4,
            ],
            [
                'nama' => 'Dr. Eko Prasetyo, M.Kom.',
                'nip' => '198210152008011001',
                'jabatan' => 'Kepala Bidang Sistem Informasi Mutu',
                'bio' => 'Pakar sistem informasi dengan fokus pada pengembangan sistem dashboard mutu.',
                'email' => 'simut.lpm@universitas.ac.id',
                'telepon' => '(021) 1234567 ext. 105',
                'urutan' => 5,
            ],
            [
                'nama' => 'Rina Kartika, S.E., M.M.',
                'nip' => '199001202015042001',
                'jabatan' => 'Staf Administrasi',
                'bio' => 'Bertanggung jawab dalam pengelolaan administrasi dan dokumentasi LPM.',
                'email' => 'admin.lpm@universitas.ac.id',
                'telepon' => '(021) 1234567 ext. 106',
                'urutan' => 6,
            ],
        ];

        foreach ($struktur as $index => $item) {
            StrukturOrganisasi::updateOrCreate(
                ['nip' => $item['nip']],
                array_merge($item, [
                    'foto' => 'struktur/person-' . ($index + 1) . '.jpg',
                    'is_active' => true,
                ])
            );
        }
    }

    private function seedKontak(): void
    {
        $contacts = [
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'telepon' => '081234567890',
                'subjek' => 'Pertanyaan tentang Audit Internal',
                'pesan' => 'Selamat pagi, saya ingin bertanya mengenai jadwal pelaksanaan audit internal untuk fakultas kami. Mohon informasinya. Terima kasih.',
                'status' => 'unread',
            ],
            [
                'nama' => 'Dewi Anggraini',
                'email' => 'dewi.anggraini@yahoo.com',
                'telepon' => '082345678901',
                'subjek' => 'Permintaan Dokumen SPMI',
                'pesan' => 'Dengan hormat, saya mahasiswa S2 yang sedang melakukan penelitian tentang SPMI. Apakah saya bisa mendapatkan dokumen kebijakan SPMI untuk bahan penelitian? Terima kasih.',
                'status' => 'read',
            ],
            [
                'nama' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@mail.com',
                'telepon' => '083456789012',
                'subjek' => 'Pendaftaran Pelatihan Auditor',
                'pesan' => 'Halo, saya dosen dari Fakultas Teknik ingin mendaftar pelatihan auditor internal. Mohon informasi persyaratan dan prosedur pendaftarannya.',
                'status' => 'replied',
                'balasan' => 'Terima kasih atas pertanyaannya. Pendaftaran pelatihan auditor dapat dilakukan melalui link berikut... Persyaratan: minimal dosen tetap dengan pengalaman minimal 2 tahun.',
            ],
            [
                'nama' => 'Sari Wulandari',
                'email' => 'sari.wulandari@email.com',
                'telepon' => '084567890123',
                'subjek' => 'Konsultasi Akreditasi Prodi',
                'pesan' => 'Selamat siang, program studi kami akan mengajukan akreditasi tahun ini. Apakah LPM menyediakan layanan pendampingan penyusunan borang?',
                'status' => 'unread',
            ],
        ];

        foreach ($contacts as $contact) {
            Kontak::create($contact);
        }
    }

    private function seedVisitors(): void
    {
        // Generate visitor data for the past 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = rand(20, 150);
            
            for ($j = 0; $j < $count; $j++) {
                Visitor::create([
                    'ip_address' => rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'page_visited' => ['/', '/berita', '/dokumen', '/galeri', '/kontak', '/tentang-lpm/profil'][rand(0, 5)],
                    'visit_date' => $date,
                    'created_at' => $date . ' ' . rand(0, 23) . ':' . rand(0, 59) . ':' . rand(0, 59),
                    'updated_at' => $date . ' ' . rand(0, 23) . ':' . rand(0, 59) . ':' . rand(0, 59),
                ]);
            }
        }
    }
}
