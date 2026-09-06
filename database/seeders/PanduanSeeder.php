<?php

namespace Database\Seeders;

use App\Models\Panduan;
use Illuminate\Database\Seeder;

class PanduanSeeder extends Seeder
{
    /**
     * Seed the complete user guide ("Buku Panduan"). Safe to re-run:
     * chapters are matched by slug via updateOrCreate.
     */
    public function run(): void
    {
        $urutan = 0;

        foreach ($this->chapters() as $chapter) {
            $urutan++;

            Panduan::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($chapter['judul'])],
                [
                    'kategori' => $chapter['kategori'],
                    'judul' => $chapter['judul'],
                    'icon' => $chapter['icon'],
                    'konten' => $chapter['konten'],
                    'urutan' => $urutan,
                    'is_active' => true,
                ]
            );
        }
    }

    private function chapters(): array
    {
        return [

            // ============================================================
            // MEMULAI
            // ============================================================
            [
                'kategori' => 'Memulai',
                'judul' => 'Tentang Sistem Ini',
                'icon' => 'bi-info-circle',
                'konten' => <<<'HTML'
<p>Sistem ini adalah aplikasi internal <strong>Lembaga Penjaminan Mutu (LPM)</strong> untuk mengelola seluruh siklus penjaminan mutu di lingkungan kampus: data program studi, status akreditasi, pelaksanaan Audit Mutu Internal (AMI), tindak lanjut temuan, serta dokumen mutu (kebijakan, SOP, manual mutu, formulir, dan panduan).</p>
<p>Tujuan utama sistem ini adalah:</p>
<ul>
<li>Menyimpan data akreditasi program studi di satu tempat dan menampilkannya secara transparan ke publik.</li>
<li>Mendigitalkan proses Audit Mutu Internal (AMI) mulai dari penjadwalan, penugasan auditor, pencatatan temuan, hingga tindak lanjut dan verifikasi penutupan.</li>
<li>Mengelola dokumen mutu dengan alur persetujuan (approval) dan riwayat versi, sehingga dokumen yang tayang ke publik sudah melalui proses review.</li>
<li>Menyediakan laporan yang bisa diunduh (CSV) untuk kebutuhan rapat tinjauan manajemen maupun asesmen eksternal.</li>
</ul>
<p>Buku panduan ini ditulis untuk semua peran pengguna (Admin, Auditor, Kepala Program Studi/Kaprodi, dan Viewer). Setiap bab akan menyebutkan peran mana saja yang relevan dengan fitur yang dibahas.</p>
HTML,
            ],
            [
                'kategori' => 'Memulai',
                'judul' => 'Cara Login & Captcha',
                'icon' => 'bi-box-arrow-in-right',
                'konten' => <<<'HTML'
<p>Untuk masuk ke panel admin, buka halaman <code>/login</code>, lalu isi:</p>
<ul>
<li><strong>Email</strong> — email akun Anda yang terdaftar di sistem.</li>
<li><strong>Password</strong> — kata sandi akun Anda.</li>
<li><strong>Captcha</strong> — sistem akan menampilkan soal penjumlahan sederhana, misalnya "4 + 7 = ?". Hitung hasilnya dan isikan angkanya pada kolom captcha. Soal ini berganti setiap kali halaman login dimuat ulang, sehingga tidak bisa dihafal — selalu baca angka yang tampil saat itu.</li>
</ul>
<p>Captcha ini adalah lapisan keamanan sederhana untuk mencegah percobaan login otomatis (bot). Jika captcha salah diisi, sistem akan menolak login dan meminta Anda mengulang dengan soal captcha yang baru.</p>
<p>Jika lupa kata sandi, hubungi Administrator untuk direset. Setelah berhasil login, Anda akan diarahkan ke <strong>Dashboard</strong> sesuai peran akun Anda.</p>
<p>Untuk keluar dari sistem, gunakan tombol <strong>Logout</strong> yang ada di pojok kanan atas panel admin.</p>
HTML,
            ],
            [
                'kategori' => 'Memulai',
                'judul' => 'Mengenal Peran & Hak Akses',
                'icon' => 'bi-people',
                'konten' => <<<'HTML'
<p>Sistem menggunakan empat peran (role) dengan hak akses berbeda. Setiap menu dan tombol aksi di panel admin otomatis disesuaikan dengan hak akses (permission) peran Anda — jika suatu menu tidak muncul di sidebar, berarti akun Anda memang tidak memiliki izin untuk fitur tersebut.</p>
<table class="table table-bordered">
<thead><tr><th>Peran</th><th>Deskripsi</th><th>Contoh hak akses</th></tr></thead>
<tbody>
<tr>
<td><strong>Administrator</strong></td>
<td>Akses penuh ke seluruh modul, termasuk pengaturan sistem, manajemen pengguna, dan peran/izin.</td>
<td>Semua permission, termasuk mengelola Standar Mutu, meng-approve dokumen, dan mengekspor laporan.</td>
</tr>
<tr>
<td><strong>Auditor</strong></td>
<td>Pelaksana Audit Mutu Internal: menerima penugasan, mencatat temuan, mereview tindak lanjut dari Kaprodi.</td>
<td>Lihat Prodi/Akreditasi, kelola Temuan, terima/tolak penugasan, review Tindak Lanjut.</td>
</tr>
<tr>
<td><strong>Kepala Program Studi (Kaprodi)</strong></td>
<td>Pihak yang di-audit. Memantau temuan pada prodinya dan mengajukan tindak lanjut perbaikan.</td>
<td>Lihat Temuan, submit Tindak Lanjut, lihat status Akreditasi & Laporan.</td>
</tr>
<tr>
<td><strong>Viewer</strong></td>
<td>Hanya dapat melihat data (read-only), tanpa hak untuk menambah, mengubah, atau menghapus.</td>
<td>Lihat Dashboard, Prodi, Akreditasi, Temuan, Tindak Lanjut, Standar Mutu, Laporan.</td>
</tr>
</tbody>
</table>
<p>Beberapa fitur juga memisahkan hak <em>"lihat"</em> dari hak <em>"aksi"</em>. Contohnya pada modul Laporan: peran Auditor/Kaprodi/Viewer bisa membuka halaman Laporan (<code>laporan-ami.view</code>), tetapi hanya Admin yang bisa benar-benar mengunduh file CSV-nya (<code>laporan.export</code>).</p>
HTML,
            ],
            [
                'kategori' => 'Memulai',
                'judul' => 'Navigasi Dasar Admin Panel',
                'icon' => 'bi-compass',
                'konten' => <<<'HTML'
<p>Setelah login, Anda akan melihat tiga bagian utama:</p>
<ul>
<li><strong>Sidebar (menu kiri)</strong> — daftar modul yang bisa Anda akses, dikelompokkan per bagian (mis. "PENJAMINAN MUTU", "AUDIT MUTU INTERNAL"). Menu yang tidak sesuai izin Anda tidak akan ditampilkan.</li>
<li><strong>Header (atas)</strong> — berisi notifikasi dan menu akun (profil, logout).</li>
<li><strong>Konten utama</strong> — area kerja tempat daftar data, formulir, dan detail ditampilkan.</li>
</ul>
<p>Beberapa menu memiliki <strong>badge</strong> (angka lingkaran berwarna) yang menunjukkan jumlah item yang butuh perhatian, misalnya jumlah Tindak Lanjut yang masih menunggu review, atau jumlah penugasan audit yang belum direspon.</p>
<p>Hampir semua daftar data (Prodi, Akreditasi, Temuan, Dokumen, dsb.) mendukung pencarian dan filter di bagian atas tabel, serta paginasi di bagian bawah jika datanya banyak.</p>
HTML,
            ],

            // ============================================================
            // PROGRAM STUDI & AKREDITASI
            // ============================================================
            [
                'kategori' => 'Program Studi & Akreditasi',
                'judul' => 'Mengelola Data Program Studi',
                'icon' => 'bi-mortarboard',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Administrator (kelola), semua peran (lihat).</em></p>
<p>Menu <strong>Program Studi</strong> menyimpan daftar prodi di kampus (nama, jenjang, dsb.). Data prodi ini menjadi rujukan di banyak modul lain: Akreditasi, Jadwal Audit, dan Temuan — semuanya dikaitkan ke sebuah prodi.</p>
<p>Untuk menambah prodi baru, klik <strong>Tambah</strong>, isi nama dan jenjang (D3/S1/S2/S3), lalu simpan. Prodi yang tidak aktif (dinonaktifkan) tidak akan muncul sebagai pilihan saat membuat jadwal audit atau akreditasi baru, namun data historisnya tetap tersimpan.</p>
HTML,
            ],
            [
                'kategori' => 'Program Studi & Akreditasi',
                'judul' => 'Mengelola Data Akreditasi',
                'icon' => 'bi-award',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Administrator (kelola), semua peran (lihat).</em></p>
<p>Menu <strong>Akreditasi</strong> mencatat status akreditasi setiap program studi: lembaga pemberi (misalnya BAN-PT/LAM), peringkat (A/B/C/Baik Sekali/Unggul, dsb.), nomor SK, tanggal SK, dan tanggal kedaluwarsa.</p>
<p><strong>Status otomatis kedaluwarsa.</strong> Anda cukup menandai akreditasi sebagai <em>aktif</em> saat menginputnya. Setiap hari sistem menjalankan pengecekan terjadwal yang membandingkan tanggal kedaluwarsa dengan tanggal hari ini — begitu tanggal tersebut terlewati, status akreditasi otomatis berubah menjadi <em>kadaluarsa</em> tanpa perlu ada yang mengubahnya manual. Ini memastikan status yang tampil, termasuk di halaman publik, selalu akurat.</p>
<p>Setiap prodi bisa memiliki lebih dari satu catatan akreditasi (riwayat), tetapi yang dianggap "akreditasi aktif saat ini" adalah catatan berstatus <em>aktif</em> dengan tanggal kedaluwarsa paling relevan.</p>
HTML,
            ],
            [
                'kategori' => 'Program Studi & Akreditasi',
                'judul' => 'Dashboard Akreditasi',
                'icon' => 'bi-speedometer2',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: semua peran.</em></p>
<p>Halaman <strong>Dashboard Akreditasi</strong> memberi ringkasan visual seluruh status akreditasi kampus: jumlah prodi terakreditasi, jumlah yang akan segera kedaluwarsa, dan jumlah yang sudah kedaluwarsa. Gunakan halaman ini untuk memantau prodi mana yang perlu segera mengurus perpanjangan akreditasi.</p>
HTML,
            ],
            [
                'kategori' => 'Program Studi & Akreditasi',
                'judul' => 'Halaman Transparansi Akreditasi Publik',
                'icon' => 'bi-globe',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: pengunjung publik (informasi), Administrator (isi konten).</em></p>
<p>Halaman publik <code>/akreditasi</code> di website kampus menampilkan tabel status akreditasi seluruh program studi secara langsung dari data yang diinput di panel admin — Program Studi, Jenjang, Lembaga, Peringkat, Berlaku Hingga, dan status (aktif/kedaluwarsa) dengan lencana warna. Karena datanya diambil langsung (bukan diketik ulang secara manual), tampilan publik ini akan selalu sinkron dengan data terbaru di modul Akreditasi, termasuk perubahan status otomatis saat masa berlaku habis.</p>
<p>Teks pengantar di atas tabel (narasi tentang komitmen mutu kampus) dapat diedit oleh Administrator melalui menu <strong>Halaman</strong> (CMS), dengan memilih halaman berslug <code>akreditasi</code>.</p>
HTML,
            ],

            // ============================================================
            // AUDIT MUTU INTERNAL (AMI)
            // ============================================================
            [
                'kategori' => 'Audit Mutu Internal (AMI)',
                'judul' => 'Periode AMI',
                'icon' => 'bi-calendar3',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Administrator (kelola), semua peran (lihat).</em></p>
<p><strong>Periode AMI</strong> adalah "wadah" waktu pelaksanaan audit, misalnya "AMI Semester Ganjil 2025/2026". Semua jadwal audit, penugasan auditor, dan temuan berada di dalam sebuah periode.</p>
<p>Sebuah periode memiliki status yang bisa diaktifkan (<strong>Activate</strong>) saat audit mulai berjalan, dan diselesaikan (<strong>Complete</strong>) setelah seluruh rangkaian audit pada periode tersebut selesai. Disarankan hanya ada satu periode aktif pada satu waktu agar tidak membingungkan saat memilih periode di form jadwal audit.</p>
HTML,
            ],
            [
                'kategori' => 'Audit Mutu Internal (AMI)',
                'judul' => 'Data Auditor',
                'icon' => 'bi-person-badge',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Administrator (kelola), semua peran (lihat).</em></p>
<p>Menu <strong>Auditor</strong> mendaftarkan siapa saja yang berperan sebagai auditor internal, terhubung ke akun pengguna (User) mereka. Hanya orang yang terdaftar di sini yang bisa dipilih/ditugaskan pada sebuah jadwal audit.</p>
<p>Pastikan akun pengguna auditor yang bersangkutan sudah dibuat terlebih dahulu (lihat bab <strong>Manajemen Pengguna & Peran</strong>) dan diberi peran <em>Auditor</em>, sebelum didaftarkan sebagai data Auditor di sini.</p>
HTML,
            ],
            [
                'kategori' => 'Audit Mutu Internal (AMI)',
                'judul' => 'Jadwal Audit & Penugasan',
                'icon' => 'bi-calendar-check',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Administrator (kelola).</em></p>
<p><strong>Jadwal Audit</strong> menentukan kapan sebuah program studi diaudit, dalam periode AMI mana, dan siapa saja auditor yang ditugaskan. Alurnya:</p>
<ol>
<li>Buat jadwal baru: pilih Prodi, Periode AMI, dan tanggal audit.</li>
<li>Tambahkan satu atau beberapa auditor ke jadwal tersebut (tombol <strong>Add Auditor</strong>). Setiap penambahan ini membuat sebuah <em>penugasan</em> untuk auditor terkait, berstatus menunggu respon.</li>
<li>Auditor yang ditugaskan akan melihat dan merespon penugasan ini lewat menu <strong>Penugasan Saya</strong> miliknya sendiri (lihat bab berikutnya).</li>
<li>Status jadwal (mis. terjadwal/berlangsung/selesai) dapat diperbarui melalui tombol <strong>Update Status</strong> seiring berjalannya audit.</li>
</ol>
<p>Jika ada auditor yang salah ditugaskan atau perlu diganti, gunakan tombol <strong>Remove Auditor</strong> pada baris penugasan yang bersangkutan, lalu tambahkan auditor pengganti.</p>
HTML,
            ],
            [
                'kategori' => 'Audit Mutu Internal (AMI)',
                'judul' => 'Penugasan Saya (Auditor)',
                'icon' => 'bi-inbox',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Auditor.</em></p>
<p>Menu <strong>Penugasan Saya</strong> adalah kotak masuk pribadi seorang auditor, berisi semua penugasan audit yang ditujukan kepadanya. Untuk setiap penugasan, auditor bisa:</p>
<ul>
<li><strong>Terima (Accept)</strong> — menyatakan kesediaan melaksanakan audit sesuai jadwal.</li>
<li><strong>Tolak (Reject)</strong> — menyatakan tidak bisa melaksanakan, misalnya karena konflik jadwal atau konflik kepentingan dengan prodi tersebut.</li>
</ul>
<p>Menu ini menampilkan badge jumlah penugasan yang masih menunggu respon, sehingga auditor tidak perlu mencari-cari satu per satu jadwal yang melibatkan dirinya.</p>
HTML,
            ],
            [
                'kategori' => 'Audit Mutu Internal (AMI)',
                'judul' => 'Standar Mutu',
                'icon' => 'bi-list-check',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Administrator (kelola), semua peran (lihat).</em></p>
<p><strong>Standar Mutu</strong> adalah master data 9 standar akreditasi (mengacu pada standar BAN-PT), misalnya "Standar 1 - Visi, Misi, Tujuan dan Strategi" hingga "Standar 9 - Luaran dan Capaian Tridharma". Data ini dipakai sebagai acuan/tag pada dua tempat:</p>
<ul>
<li><strong>Temuan Audit</strong> — setiap temuan dikaitkan ke satu standar mutu, memudahkan rekap "standar mana yang paling banyak temuannya".</li>
<li><strong>Dokumen</strong> — dokumen mutu (opsional) bisa ditandai termasuk bukti standar mutu yang mana.</li>
</ul>
<p>Administrator dapat menambah, mengubah urutan, menonaktifkan, atau menghapus standar mutu. Standar mutu yang masih dipakai oleh temuan atau dokumen <strong>tidak dapat dihapus</strong> — sistem akan menampilkan pesan penolakan agar data yang sudah terhubung tidak menjadi rusak (data terhubung dari tabel lain hilang rujukannya).</p>
HTML,
            ],
            [
                'kategori' => 'Audit Mutu Internal (AMI)',
                'judul' => 'Mencatat Temuan Audit',
                'icon' => 'bi-search',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Auditor (catat/edit), Kaprodi & Viewer (lihat).</em></p>
<p>Selama atau setelah audit berlangsung, auditor mencatat <strong>Temuan</strong> pada menu Temuan. Setiap temuan memiliki:</p>
<ul>
<li><strong>Standar</strong> — dipilih dari master data Standar Mutu.</li>
<li><strong>Kategori temuan</strong> — <code>mayor</code>, <code>minor</code>, <code>observasi</code>, atau <code>rekomendasi</code>, menunjukkan tingkat keseriusan.</li>
<li><strong>Deskripsi</strong> — uraian temuan.</li>
<li><strong>Bukti</strong> — catatan teks pendukung, <em>dan/atau</em> lampiran berkas bukti (foto, dokumen PDF/Word, maksimal 10 MB) yang bisa diunggah dan diunduh kembali kapan saja.</li>
<li><strong>Batas tindak lanjut</strong> — tenggat waktu bagi prodi untuk menindaklanjuti.</li>
<li><strong>Status</strong> — <code>open</code> (baru dibuat) → <code>in_progress</code> (sedang ditindaklanjuti) → <code>closed</code> (tindak lanjut disetujui) → <code>verified</code> (diverifikasi tuntas oleh auditor).</li>
</ul>
<p>Temuan yang sudah melewati batas tindak lanjut namun belum <code>closed</code>/<code>verified</code> akan ditandai <strong>overdue (terlambat)</strong> secara otomatis oleh sistem, membantu auditor memantau prodi mana yang perlu diingatkan.</p>
<p>Mengganti berkas bukti pada temuan yang sudah ada akan otomatis menghapus berkas lama dari penyimpanan dan menggantinya dengan yang baru.</p>
HTML,
            ],
            [
                'kategori' => 'Audit Mutu Internal (AMI)',
                'judul' => 'Tindak Lanjut Temuan',
                'icon' => 'bi-clipboard-check',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Kaprodi (submit), Auditor (review), semua peran (lihat).</em></p>
<p>Setelah menerima sebuah temuan, <strong>Kepala Program Studi</strong> menyusun rencana/bukti perbaikan dan mengirimkannya sebagai <strong>Tindak Lanjut</strong> melalui menu Tindak Lanjut. Alur statusnya:</p>
<ol>
<li><code>submitted</code> — Kaprodi mengajukan tindak lanjut, menunggu direview auditor.</li>
<li><code>reviewed</code> — auditor sudah meninjau namun belum memutuskan (opsional, dipakai jika perlu diskusi lanjutan).</li>
<li><code>approved</code> — auditor menyetujui tindak lanjut. Temuan terkait otomatis berubah status menjadi <code>closed</code>.</li>
<li><code>rejected</code> — auditor menolak, disertai catatan reviewer yang menjelaskan kekurangannya, sehingga Kaprodi dapat merevisi dan mengajukan ulang.</li>
</ol>
<p>Auditor bisa melihat semua tindak lanjut yang masih menunggu review lewat filter/halaman <strong>Pending Review</strong>, dan menu Tindak Lanjut di sidebar menampilkan badge jumlah yang menunggu review agar tidak terlewat.</p>
<p>Setelah temuan berstatus <code>closed</code>, auditor masih dapat melakukan verifikasi akhir untuk mengubah status menjadi <code>verified</code> — menandakan tindak lanjut sudah dicek benar-benar dilaksanakan, bukan hanya disetujui di atas kertas.</p>
HTML,
            ],

            // ============================================================
            // MANAJEMEN DOKUMEN
            // ============================================================
            [
                'kategori' => 'Manajemen Dokumen',
                'judul' => 'Jenis Dokumen',
                'icon' => 'bi-folder2',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Administrator.</em></p>
<p><strong>Jenis Dokumen</strong> adalah kategori pengelompokan dokumen mutu, misalnya Kebijakan, Manual Mutu, Formulir, SOP, Laporan, Panduan, dan SK & Surat. Setiap jenis memiliki ikon sendiri agar mudah dikenali saat ditampilkan sebagai filter di halaman dokumen publik maupun di panel admin.</p>
HTML,
            ],
            [
                'kategori' => 'Manajemen Dokumen',
                'judul' => 'Upload & Alur Persetujuan Dokumen',
                'icon' => 'bi-file-earmark-arrow-up',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Administrator.</em></p>
<p>Setiap dokumen yang diunggah (judul, berkas, jenis dokumen, kategori, dan opsional tag Standar Mutu) melewati alur persetujuan sebelum tampil ke publik:</p>
<table class="table table-bordered">
<thead><tr><th>Status</th><th>Arti</th></tr></thead>
<tbody>
<tr><td><span class="badge bg-secondary">draft</span></td><td>Baru diunggah, belum diajukan untuk direview. Belum tampil di halaman dokumen publik.</td></tr>
<tr><td><span class="badge bg-info">submitted</span></td><td>Sudah diajukan (tombol <strong>Ajukan untuk Review</strong>), menunggu keputusan reviewer.</td></tr>
<tr><td><span class="badge bg-success">approved</span></td><td>Disetujui reviewer. Jika dokumen juga berstatus aktif, dokumen ini langsung tampil dan bisa diunduh di halaman publik <code>/dokumen</code>.</td></tr>
<tr><td><span class="badge bg-danger">rejected</span></td><td>Ditolak reviewer disertai catatan alasan penolakan, yang wajib diisi saat menolak.</td></tr>
</tbody>
</table>
<p>Buka halaman detail sebuah dokumen (ikon mata pada daftar dokumen) untuk melihat status, siapa yang mengunggah, siapa yang mereview, catatan reviewer, serta tombol aksi <strong>Ajukan</strong>/<strong>Setujui</strong>/<strong>Tolak</strong> sesuai status saat ini.</p>
<p><strong>Dokumen lama (sebelum fitur ini ada)</strong> otomatis dianggap sudah <code>approved</code> saat fitur ini diaktifkan, sehingga tidak ada dokumen yang tiba-tiba hilang dari halaman publik.</p>
HTML,
            ],
            [
                'kategori' => 'Manajemen Dokumen',
                'judul' => 'Riwayat Versi Dokumen',
                'icon' => 'bi-clock-history',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Administrator.</em></p>
<p>Saat berkas sebuah dokumen diganti melalui menu <strong>Edit</strong>, sistem <strong>tidak menghapus berkas lama</strong>. Berkas lama otomatis diarsipkan ke Riwayat Versi dengan nomor versi berurutan (v1, v2, dst.), lengkap dengan siapa yang mengunggahnya dan kapan. Berkas versi lama tetap bisa diunduh kapan saja dari halaman detail dokumen.</p>
<p>Karena berkas berubah, dokumen yang sebelumnya berstatus <code>approved</code> akan otomatis kembali menjadi <code>draft</code> — konsekuensinya dokumen ini juga akan hilang sementara dari halaman publik sampai versi barunya diajukan dan disetujui ulang. Ini disengaja: perubahan isi dokumen resmi seharusnya melalui review ulang, bukan langsung tayang tanpa persetujuan.</p>
HTML,
            ],
            [
                'kategori' => 'Manajemen Dokumen',
                'judul' => 'Dokumen Publik',
                'icon' => 'bi-download',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: pengunjung publik.</em></p>
<p>Halaman <code>/dokumen</code> di website kampus menampilkan seluruh dokumen yang <strong>aktif</strong> dan berstatus <strong>approved</strong>, lengkap dengan pencarian judul/deskripsi dan filter per kategori. Pengunjung dapat mengunduh berkas langsung dari sini; setiap unduhan menambah penghitung unduhan (download count) dokumen yang bersangkutan, yang bisa dilihat Administrator di daftar dokumen panel admin.</p>
HTML,
            ],

            // ============================================================
            // LAPORAN
            // ============================================================
            [
                'kategori' => 'Laporan',
                'judul' => 'Export Laporan CSV',
                'icon' => 'bi-file-earmark-bar-graph',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Administrator (unduh), semua peran (lihat menu).</em></p>
<p>Menu <strong>Laporan</strong> menyediakan tiga jenis ekspor data dalam format CSV (bisa dibuka langsung di Excel):</p>
<ul>
<li><strong>Laporan Temuan AMI</strong> — seluruh temuan beserta program studi, periode, standar, kategori, status, dan auditor penanggung jawab.</li>
<li><strong>Laporan Tindak Lanjut</strong> — seluruh tindak lanjut beserta status, siapa yang mengajukan, siapa yang mereview, dan catatan reviewer.</li>
<li><strong>Laporan Akreditasi</strong> — status akreditasi seluruh program studi beserta lembaga, peringkat, nomor SK, dan tanggal berlaku.</li>
</ul>
<p>Semua peran yang login (Auditor/Kaprodi/Viewer) dapat <em>membuka</em> halaman Laporan untuk melihat menu ini, namun tombol <strong>Export CSV</strong> hanya bisa dijalankan oleh <strong>Administrator</strong>. Ini memastikan data mentah dalam jumlah besar hanya bisa diunduh oleh pihak yang berwenang.</p>
<p>File CSV yang dihasilkan sudah menyertakan penanda encoding UTF-8, sehingga karakter Indonesia (misalnya "kadaluarsa", nama dengan huruf khusus) tetap terbaca benar saat dibuka di Microsoft Excel.</p>
HTML,
            ],

            // ============================================================
            // ADMINISTRASI SISTEM
            // ============================================================
            [
                'kategori' => 'Administrasi Sistem',
                'judul' => 'Manajemen Pengguna & Peran',
                'icon' => 'bi-person-gear',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Administrator.</em></p>
<p>Menu <strong>Pengguna (Users)</strong> digunakan untuk membuat akun login bagi setiap pegawai/dosen yang perlu mengakses sistem, dan menentukan perannya (Admin/Auditor/Kaprodi/Viewer). Peran inilah yang menentukan menu dan aksi apa saja yang bisa diakses akun tersebut, sesuai penjelasan di bab <strong>Mengenal Peran & Hak Akses</strong>.</p>
<p>Untuk kebutuhan yang lebih detail, Administrator juga dapat mengatur <strong>Roles & Permissions</strong> — menambah izin baru atau mengubah kombinasi izin yang dimiliki sebuah peran, tanpa harus mengubah kode program.</p>
HTML,
            ],
            [
                'kategori' => 'Administrasi Sistem',
                'judul' => 'Manajemen Menu',
                'icon' => 'bi-list',
                'konten' => <<<'HTML'
<p><em>Relevan untuk: Administrator.</em></p>
<p>Menu sidebar panel admin (termasuk buku panduan ini) dikelola secara dinamis lewat menu <strong>Menu Management</strong>. Setiap item menu memiliki nama, ikon, route tujuan, urutan tampil, dan permission yang menentukan peran mana yang boleh melihatnya. Susunan menu bisa diatur ulang urutannya lewat fitur <strong>reorder</strong> (drag & drop).</p>
<p>Setelah data menu diubah, sistem menyimpan cache susunan menu untuk mempercepat tampilan; perubahan akan otomatis terlihat setelah disimpan karena cache ikut diperbarui.</p>
HTML,
            ],

            // ============================================================
            // BANTUAN
            // ============================================================
            [
                'kategori' => 'Bantuan',
                'judul' => 'Pertanyaan Umum (FAQ)',
                'icon' => 'bi-question-circle',
                'konten' => <<<'HTML'
<p><strong>Kenapa suatu menu tidak muncul di sidebar saya?</strong><br>Menu hanya tampil jika peran akun Anda memiliki izin (permission) terkait. Hubungi Administrator jika menurut Anda seharusnya memiliki akses tersebut.</p>
<p><strong>Kenapa dokumen yang baru saya unggah belum muncul di halaman publik?</strong><br>Dokumen baru selalu dimulai berstatus <em>draft</em>. Ajukan untuk direview (tombol <strong>Ajukan untuk Review</strong>), lalu tunggu disetujui (<em>approved</em>) oleh reviewer. Dokumen juga harus berstatus aktif untuk bisa tampil publik.</p>
<p><strong>Saya mengganti berkas dokumen yang sudah disetujui, kenapa jadi hilang dari halaman publik?</strong><br>Ini perilaku yang disengaja — perubahan berkas dokumen resmi butuh direview ulang. Ajukan kembali dokumennya untuk direview agar tampil lagi. Versi lama tetap tersimpan dan bisa diunduh dari riwayat versi.</p>
<p><strong>Kenapa status akreditasi sebuah prodi berubah sendiri menjadi "kadaluarsa"?</strong><br>Sistem memeriksa tanggal kedaluwarsa setiap hari secara otomatis. Jika tanggal tersebut sudah lewat, status diperbarui otomatis agar data akreditasi yang ditampilkan — termasuk ke publik — selalu akurat tanpa perlu diubah manual satu per satu.</p>
<p><strong>Saya Auditor/Kaprodi/Viewer, kenapa tidak bisa mengunduh Laporan CSV padahal menunya terlihat?</strong><br>Melihat menu Laporan dan mengunduh isinya adalah dua izin terpisah. Saat ini hanya Administrator yang memiliki izin unduh (export), untuk menjaga data mentah dalam jumlah besar tidak tersebar bebas.</p>
<p><strong>Berkas apa saja yang boleh diunggah untuk bukti temuan atau dokumen?</strong><br>Format yang didukung adalah PDF, Word (doc/docx), dan untuk bukti temuan juga gambar (jpg/jpeg/png), dengan ukuran maksimal 10 MB per berkas.</p>
<p>Jika pertanyaan Anda belum terjawab di sini, hubungi Administrator LPM.</p>
HTML,
            ],
        ];
    }
}
