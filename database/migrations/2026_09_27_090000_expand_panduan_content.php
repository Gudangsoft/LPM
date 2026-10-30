<?php

use App\Models\Panduan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Melengkapi Buku Panduan agar mencakup seluruh modul sistem (termasuk 8 menu
     * SPMI dan Aturan AMI yang baru difungsikan, dashboard, konten publik, dan
     * pengaturan situs) - bukan hanya modul inti AMI/Dokumen/Akreditasi yang sudah
     * ada. Setiap bab baru ditulis dengan langkah bernomor + slot screenshot
     * ("[SS]") yang bisa diisi belakangan lewat editor Buku Panduan, karena tidak
     * ada cara otomatis untuk mengambil screenshot sungguhan dari sini.
     *
     * Idempotent: setiap bab dicocokkan lewat `judul` - kalau sudah ada, hanya
     * `urutan` (dan, untuk satu bab yang sudah usang, isinya) yang diperbarui;
     * kalau belum ada, baris baru dibuat.
     */
    public function up(): void
    {
        $ss = fn (string $desc) => '<div class="my-3 p-3 rounded" style="border:2px dashed #94a3b8;background:#f8fafc;">'
            . '<p class="mb-0 text-muted"><strong>[SS]</strong> — <em>' . $desc . '</em></p></div>';

        // Isi bab BARU (judul => [kategori, icon, konten]). Urutan ditentukan oleh
        // posisi judul di $order di bawah, bukan oleh urutan array ini.
        $chapters = [
            // ---- Memulai (tambahan) --------------------------------------------
            'Profil & Keamanan Akun Saya' => ['Memulai', 'bi-person-circle', '
                <p><em>Relevan untuk: Semua peran.</em></p>
                <p>Setiap pengguna dapat mengelola akunnya sendiri lewat halaman <strong>Profil</strong>, diakses lewat menu di pojok kanan atas header (foto/avatar akun Anda).</p>
                <ol>
                    <li>Klik foto akun di pojok kanan atas, lalu pilih <strong>Profil Saya</strong>.</li>
                    <li>Pada tab <strong>Informasi Akun</strong>, ubah nama, email, atau unggah foto profil baru.</li>
                    ' . $ss('Halaman Profil - tab Informasi Akun dengan form nama/email/upload foto') . '
                    <li>Pada tab <strong>Ubah Password</strong>, masukkan password lama lalu password baru dua kali untuk konfirmasi.</li>
                    <li>Pada tab <strong>Sesi Aktif</strong>, lihat daftar perangkat/browser yang sedang login dengan akun Anda (lengkap dengan waktu aktivitas terakhir), dan klik <strong>Akhiri Sesi</strong> pada perangkat yang tidak dikenali.</li>
                    ' . $ss('Halaman Profil - tab Sesi Aktif dengan daftar perangkat dan tombol akhiri sesi') . '
                </ol>
                <p>Gunakan tab <strong>Zona Berbahaya</strong> dengan hati-hati - opsi di sana bersifat permanen (mis. menghapus akun).</p>
            '],

            // ---- Dashboard (kategori baru) --------------------------------------
            'Dashboard Utama' => ['Dashboard', 'bi-speedometer2', '
                <p><em>Relevan untuk: Semua peran (isi kartu menyesuaikan hak akses).</em></p>
                <p>Halaman pertama setelah login. Menampilkan ringkasan kondisi mutu institusi secara sekilas: jumlah program studi, status akreditasi, periode AMI yang sedang berjalan, dan notifikasi yang perlu ditindaklanjuti.</p>
                ' . $ss('Halaman Dashboard - kartu ringkasan dan grafik utama') . '
                <p>Klik lonceng notifikasi di header untuk melihat pengingat otomatis (mis. evaluasi diri yang belum dikirim, temuan yang mendekati tenggat, akreditasi yang akan kedaluwarsa) - lihat juga bab <strong>Aturan AMI</strong> untuk mengatur kapan pengingat ini dikirim.</p>
            '],
            'Dashboard AMI' => ['Dashboard', 'bi-graph-up', '
                <p><em>Relevan untuk: Administrator, Auditor, Kaprodi, Viewer.</em></p>
                <p>Diakses lewat menu <strong>AMI &gt; Dashboard AMI</strong>. Berbeda dari Dashboard Utama, halaman ini fokus khusus ke progres siklus Audit Mutu Internal pada periode yang sedang aktif (Kaprodi otomatis hanya melihat data prodinya).</p>
                <ol>
                    <li>Pilih periode AMI dari dropdown filter di atas (default: periode berstatus <code>aktif</code>).</li>
                    <li>Kartu ringkasan menampilkan: jumlah unit/prodi terjadwal, jumlah auditor aktif, total temuan, temuan selesai vs belum selesai, dan persentase penyelesaian tindak lanjut (RTL).</li>
                    ' . $ss('Dashboard AMI - kartu ringkasan periode aktif') . '
                    <li>Grafik di bawahnya menampilkan progres audit per status, status temuan, temuan per unit/prodi, temuan per standar mutu, dan persentase penyelesaian RTL per unit.</li>
                    ' . $ss('Dashboard AMI - grafik status temuan dan temuan per standar') . '
                </ol>
            '],

            // ---- Audit Mutu Internal (AMI) — tambahan --------------------------
            'Instrumen Audit' => ['Audit Mutu Internal (AMI)', 'bi-list-check', '
                <p><em>Relevan untuk: Administrator (kelola), Auditor/Kaprodi/Viewer (lihat).</em></p>
                <p>Instrumen Audit adalah kumpulan <strong>butir/pertanyaan</strong> di bawah setiap Standar Mutu yang akan diisi auditee (lewat Evaluasi Diri) dan dinilai auditor (lewat Lembar Kerja Audit). Diakses lewat menu <strong>Standar Mutu &gt; Instrumen Audit</strong>.</p>
                <ol>
                    <li>Pilih Standar Mutu dari filter di atas tabel.</li>
                    <li>Klik <strong>+ Tambah Butir</strong> untuk menambah pertanyaan baru.</li>
                    ' . $ss('Form tambah butir instrumen - field kode, pertanyaan, indikator, bobot, target, jenis bukti') . '
                    <li>Isi <strong>Kode</strong> (mis. "1.1"), <strong>Pertanyaan</strong>, <strong>Indikator</strong> penilaian, <strong>Bobot</strong> (untuk perhitungan skor akhir), <strong>Target</strong> (mis. "&ge; 3.5"), dan <strong>Jenis Bukti</strong> yang diharapkan (mis. dokumen, foto, tautan).</li>
                    <li>Urutan tampil butir bisa diatur lewat kolom <strong>Urutan</strong>, dan butir yang tidak dipakai lagi dapat dinonaktifkan tanpa perlu dihapus (agar riwayat penilaian lama tetap utuh).</li>
                </ol>
            '],
            'Evaluasi Diri (Auditee)' => ['Audit Mutu Internal (AMI)', 'bi-pencil-square', '
                <p><em>Relevan untuk: Kaprodi (isi), Auditor/Administrator (lihat).</em></p>
                <p>Sebelum audit lapangan berlangsung, auditee (Kaprodi) mengisi <strong>Evaluasi Diri</strong> untuk setiap jadwal audit yang menyasar prodinya - menilai diri sendiri terhadap tiap butir instrumen sebelum dinilai auditor.</p>
                <ol>
                    <li>Buka menu <strong>AMI &gt; Evaluasi Diri</strong>, lalu pilih jadwal audit yang ingin diisi.</li>
                    ' . $ss('Halaman Evaluasi Diri - daftar butir instrumen per standar dengan kolom nilai mandiri') . '
                    <li>Untuk tiap butir, isi <strong>nilai mandiri</strong> dan unggah <strong>berkas bukti</strong> pendukung (bisa lebih dari satu berkas per butir).</li>
                    ' . $ss('Form isi satu butir instrumen - nilai mandiri dan tombol unggah bukti') . '
                    <li>Data tersimpan otomatis setiap kali diubah, sehingga bisa dilanjutkan bertahap.</li>
                    <li>Setelah semua butir terisi, klik <strong>Kirim (Submit)</strong> - setelah dikirim, evaluasi diri terkunci dan tidak bisa diedit lagi kecuali dibuka ulang oleh Administrator.</li>
                </ol>
                <p>Jadwal audit yang belum dikirim evaluasi dirinya menjelang tanggal audit akan otomatis diingatkan lewat notifikasi (lihat bab <strong>Aturan AMI</strong> untuk mengatur berapa hari sebelumnya pengingat ini dikirim).</p>
            '],
            'Lembar Kerja Audit (Auditor)' => ['Audit Mutu Internal (AMI)', 'bi-clipboard-data', '
                <p><em>Relevan untuk: Auditor (isi), Administrator/Viewer (lihat).</em></p>
                <p>Saat audit lapangan berlangsung, auditor menilai setiap butir instrumen lewat <strong>Lembar Kerja Audit</strong>, sambil memvalidasi bukti yang diunggah auditee pada tahap Evaluasi Diri.</p>
                <ol>
                    <li>Buka menu <strong>AMI &gt; Lembar Kerja Audit</strong>, pilih jadwal audit yang sedang ditugaskan ke Anda.</li>
                    ' . $ss('Halaman Lembar Kerja Audit - daftar butir dengan nilai mandiri auditee di samping kolom nilai auditor') . '
                    <li>Untuk tiap butir, bandingkan nilai mandiri auditee dengan bukti yang dilampirkan, lalu isi <strong>nilai auditor</strong> dan <strong>catatan</strong>.</li>
                    <li>Klik <strong>Validasi</strong> pada tiap berkas bukti untuk menandainya sudah diperiksa.</li>
                    ' . $ss('Tombol validasi bukti pada satu lampiran berkas') . '
                    <li>Hasil penilaian di sinilah yang menjadi dasar pencatatan <strong>Temuan Audit</strong> jika ditemukan ketidaksesuaian.</li>
                </ol>
            '],
            'Verifikasi RTL' => ['Audit Mutu Internal (AMI)', 'bi-check2-square', '
                <p><em>Relevan untuk: Auditor (verifikasi), Kaprodi (lihat status).</em></p>
                <p>Setelah Kaprodi mengajukan Tindak Lanjut (RTL) atas sebuah temuan, auditor yang bersangkutan perlu <strong>memverifikasi</strong> apakah tindak lanjut tersebut sudah benar-benar menyelesaikan temuan.</p>
                <ol>
                    <li>Buka menu <strong>AMI &gt; Verifikasi RTL</strong> - halaman ini hanya menampilkan RTL yang berstatus menunggu review.</li>
                    ' . $ss('Halaman Verifikasi RTL - daftar RTL menunggu verifikasi dengan tombol Setujui/Tolak') . '
                    <li>Baca uraian tindak lanjut dan bukti penyelesaian yang dilampirkan Kaprodi.</li>
                    <li>Klik <strong>Setujui</strong> jika tindak lanjut sudah memadai (temuan otomatis berubah status menjadi <code>verified</code>/tuntas), atau <strong>Tolak</strong> disertai catatan jika belum memadai (Kaprodi perlu mengajukan ulang).</li>
                </ol>
            '],
            'Monitoring AMI' => ['Audit Mutu Internal (AMI)', 'bi-activity', '
                <p><em>Relevan untuk: Administrator, Auditor, Kaprodi (data milik sendiri), Viewer.</em></p>
                <p>Halaman <strong>Monitoring</strong> (di bawah P-3 Pengendalian) memberi gambaran ringkas seberapa jauh proses pengendalian mutu berjalan, tanpa perlu membuka satu per satu daftar temuan.</p>
                <ol>
                    <li>Kartu ringkasan menampilkan jumlah temuan per status (<code>open</code>, <code>in_progress</code>, <code>overdue</code>/terlambat, <code>closed</code>) serta status RTL (<code>submitted</code>, <code>approved</code>, <code>rejected</code>).</li>
                    ' . $ss('Halaman Monitoring - kartu status temuan dan RTL') . '
                    <li>Tabel <strong>rollup per prodi</strong> menampilkan total temuan, jumlah closed, open, overdue, dan persentase penyelesaian tiap prodi - berguna untuk melihat prodi mana yang tertinggal.</li>
                    <li>Tabel <strong>temuan telat</strong> mendaftar seluruh temuan yang sudah melewati batas tindak lanjut namun belum tuntas, lengkap dengan berapa hari keterlambatannya.</li>
                    ' . $ss('Tabel rollup per prodi dan tabel temuan telat') . '
                </ol>
            '],
            'Dokumen/Bukti Audit' => ['Audit Mutu Internal (AMI)', 'bi-folder2-open', '
                <p><em>Relevan untuk: Administrator, Auditor, Kaprodi, Viewer.</em></p>
                <p>Menu <strong>AMI &gt; Dokumen/Bukti</strong> menampilkan seluruh berkas bukti yang pernah diunggah selama proses Evaluasi Diri maupun Lembar Kerja Audit, dikumpulkan dalam satu daftar agar mudah ditelusuri tanpa harus membuka tiap jadwal audit satu per satu.</p>
                <ol>
                    <li>Gunakan filter jadwal/periode/prodi untuk mempersempit pencarian.</li>
                    ' . $ss('Halaman Dokumen/Bukti Audit - daftar berkas dengan filter dan tombol unduh') . '
                    <li>Klik nama berkas untuk mengunduh atau melihatnya kembali.</li>
                </ol>
                <p>Halaman ini bersifat telusur saja (read-only) - untuk mengunggah bukti baru, lakukan lewat halaman Evaluasi Diri atau Lembar Kerja Audit terkait.</p>
            '],
            'Rapat Tinjauan Manajemen (RTM)' => ['Audit Mutu Internal (AMI)', 'bi-people', '
                <p><em>Relevan untuk: Administrator (kelola), semua peran ber-izin <code>rtm.view</code> (lihat).</em></p>
                <p>RTM adalah tahap <strong>Peningkatan (P-4)</strong> dalam siklus PPEPP - forum pimpinan meninjau seluruh hasil AMI periode berjalan dan menetapkan keputusan/rekomendasi perbaikan.</p>
                <ol>
                    <li>Buka menu <strong>AMI &gt; Rapat Tinjauan Manajemen</strong>, klik <strong>+ Tambah RTM</strong>.</li>
                    <li>Isi judul rapat, tanggal, tempat, pemimpin rapat, dan periode AMI yang dibahas.</li>
                    ' . $ss('Form tambah RTM - judul, tanggal, tempat, pemimpin, periode AMI') . '
                    <li>Di halaman detail RTM, klik <strong>Buat Ringkasan Otomatis</strong> untuk menghasilkan rangkuman capaian AMI periode terkait (jumlah temuan, status penyelesaian, dll) sebagai bahan diskusi.</li>
                    <li>Tambahkan baris <strong>Agenda</strong> pembahasan, lalu catat <strong>Keputusan &amp; Rekomendasi</strong> hasil rapat - tiap keputusan punya penanggung jawab (PIC), target tanggal, dan status (belum/proses/selesai).</li>
                    ' . $ss('Halaman detail RTM - daftar agenda dan tabel keputusan/rekomendasi') . '
                </ol>
                <p>Seluruh keputusan RTM dari semua rapat juga bisa dilihat terkumpul di satu tempat lewat menu <strong>Rencana Peningkatan Mutu</strong> (lihat kategori SPMI).</p>
            '],
            'Analitik Mutu' => ['Audit Mutu Internal (AMI)', 'bi-bar-chart-line', '
                <p><em>Relevan untuk: Administrator, Auditor, Viewer.</em></p>
                <p>Menu <strong>AMI &gt; Analitik Mutu</strong> menyediakan analisis lebih mendalam dibanding Dashboard AMI: tren capaian mutu lintas periode, temuan yang berulang di standar/unit yang sama, serta perbandingan antar periode atau antar prodi.</p>
                ' . $ss('Halaman Analitik Mutu - grafik tren dan perbandingan periode/unit') . '
                <p>Gunakan halaman ini saat menyiapkan bahan RTM atau laporan tahunan, karena datanya sudah diagregasi lintas periode, bukan hanya periode yang sedang aktif.</p>
            '],
            'Audit Trail' => ['Audit Mutu Internal (AMI)', 'bi-clock-history', '
                <p><em>Relevan untuk: Administrator.</em></p>
                <p>Setiap perubahan pada data AMI yang sensitif (Periode, Jadwal, Penugasan, Temuan, Tindak Lanjut, Auditor, Standar Mutu, Instrumen, dst) tercatat otomatis oleh sistem di <strong>Audit Trail</strong> - siapa mengubah apa, kapan, dan perubahan field apa saja yang terjadi.</p>
                <ol>
                    <li>Buka menu <strong>AMI &gt; Audit Trail</strong>.</li>
                    <li>Gunakan filter jenis entitas, pengguna, jenis aksi (dibuat/diubah/dihapus/diverifikasi/disetujui/ditolak, dst), dan rentang tanggal untuk menelusuri riwayat tertentu.</li>
                    ' . $ss('Halaman Audit Trail - tabel log dengan filter entitas, pengguna, dan tanggal') . '
                </ol>
                <p>Log ini bersifat permanen dan tidak bisa diubah lewat antarmuka - berguna untuk keperluan akuntabilitas dan investigasi jika terjadi kejanggalan data.</p>
            '],

            // ---- SPMI (Siklus PPEPP) — kategori baru ----------------------------
            'Kebijakan, Manual & Formulir SPMI' => ['SPMI (Siklus PPEPP)', 'bi-journal-bookmark', '
                <p><em>Relevan untuk: Administrator (kelola), semua peran ber-izin <code>dokumen.view</code> (lihat).</em></p>
                <p>Ketiga menu ini (di bawah P-1 Penetapan) sebenarnya adalah <strong>modul Dokumen yang sama</strong>, hanya disaring otomatis berdasarkan jenis dokumennya - jadi cara pakainya identik dengan bab <strong>Upload &amp; Alur Persetujuan Dokumen</strong>, tinggal dibuka lewat menu yang sesuai supaya jenis dokumennya sudah terpilih otomatis.</p>
                <ul>
                    <li><strong>Kebijakan SPMI</strong> - dokumen kebijakan mutu tingkat institusi.</li>
                    <li><strong>Manual SPMI</strong> - manual/pedoman pelaksanaan SPMI.</li>
                    <li><strong>Formulir</strong> - berkas formulir/template kerja SPMI yang siap dipakai unit.</li>
                </ul>
                ' . $ss('Halaman Kebijakan SPMI - daftar dokumen dengan jenis "Kebijakan SPMI" sudah terfilter') . '
            '],
            'Sasaran Mutu' => ['SPMI (Siklus PPEPP)', 'bi-bullseye', '
                <p><em>Relevan untuk: Kaprodi (kelola milik prodi sendiri), Administrator (kelola semua termasuk institusional).</em></p>
                <p>Sasaran Mutu mencatat target kuantitatif/kualitatif per Standar Mutu untuk tiap tahun akademik, beserta realisasi dan status pencapaiannya - bagian dari tahap <strong>Pelaksanaan (P-2)</strong>.</p>
                <ol>
                    <li>Buka menu <strong>SPMI &gt; Sasaran Mutu</strong>, klik <strong>+ Tambah Sasaran</strong>.</li>
                    <li>Pilih Standar Mutu, lalu pilih Program Studi - atau <strong>kosongkan Program Studi</strong> jika sasaran ini berlaku institusional (semua prodi), bukan milik satu prodi tertentu.</li>
                    ' . $ss('Form tambah sasaran mutu - pilihan standar, prodi (opsional), uraian, target, satuan') . '
                    <li>Isi uraian sasaran, indikator, target (mis. "3.30" atau "&ge; 75"), dan satuan (poin/%/dll).</li>
                    <li>Setelah periode berjalan dan hasilnya diketahui, edit kembali sasaran tersebut untuk mengisi <strong>Realisasi</strong> dan mengubah <strong>Status</strong> menjadi Tercapai atau Tidak Tercapai.</li>
                    ' . $ss('Daftar sasaran mutu dengan badge status Tercapai/Tidak Tercapai/Belum Dievaluasi') . '
                </ol>
                <p>Kaprodi hanya bisa membuat/mengubah sasaran milik prodinya sendiri, tetapi tetap bisa melihat sasaran prodi lain dan sasaran institusional.</p>
            '],
            'Pengendalian Dokumen' => ['SPMI (Siklus PPEPP)', 'bi-folder-check', '
                <p><em>Relevan untuk: Administrator.</em></p>
                <p>Halaman ini adalah <strong>daftar induk kendali mutu</strong> atas seluruh dokumen SPMI (lintas jenis - bukan hanya Kebijakan/Manual/Formulir), menampilkan versi berjalan, tanggal terakhir ditinjau, dan kapan dokumen tersebut harus ditinjau ulang.</p>
                <ol>
                    <li>Buka menu <strong>SPMI &gt; Pengendalian Dokumen</strong>.</li>
                    <li>Gunakan filter Jenis Dokumen atau Status untuk mempersempit tampilan, termasuk filter khusus <strong>Jatuh Tempo Tinjau</strong> untuk melihat dokumen yang sudah lewat jadwal tinjau ulangnya.</li>
                    ' . $ss('Halaman Pengendalian Dokumen - tabel dengan kolom versi, terakhir ditinjau, siklus tinjau') . '
                    <li>Pada kolom <strong>Siklus Tinjau</strong>, isi jumlah bulan (mis. 12, 24, 36) lalu klik tombol centang - sistem akan menghitung otomatis tanggal jatuh tempo tinjauan berikutnya berdasarkan tanggal dokumen terakhir disetujui/ditinjau.</li>
                    <li>Klik judul dokumen untuk membuka dan mengeditnya langsung di modul Dokumen (lihat bab <strong>Upload &amp; Alur Persetujuan Dokumen</strong>).</li>
                </ol>
            '],
            'Rencana Peningkatan Mutu' => ['SPMI (Siklus PPEPP)', 'bi-graph-up', '
                <p><em>Relevan untuk: Administrator, semua peran ber-izin <code>rtm.view</code>.</em></p>
                <p>Merangkum <strong>semua keputusan dan rekomendasi</strong> dari seluruh Rapat Tinjauan Manajemen (RTM) dalam satu daftar - bagian dari tahap <strong>Peningkatan (P-4)</strong> - tanpa perlu membuka RTM satu per satu.</p>
                <ol>
                    <li>Buka menu <strong>SPMI &gt; Rencana Peningkatan Mutu</strong>.</li>
                    <li>Gunakan filter Periode AMI, Status, PIC (penanggung jawab), atau kata kunci untuk mencari keputusan tertentu.</li>
                    ' . $ss('Halaman Rencana Peningkatan Mutu - tabel keputusan lintas RTM dengan filter') . '
                    <li>Ubah langsung status tiap keputusan (Belum/Proses/Selesai) lewat dropdown di kolom paling kanan - perubahan tersimpan otomatis.</li>
                </ol>
                <p>Untuk menambah atau menghapus keputusan baru, buka RTM terkait lewat menu <strong>Rapat Tinjauan Manajemen</strong> - halaman ini murni untuk memantau lintas-RTM.</p>
            '],
            'Benchmarking' => ['SPMI (Siklus PPEPP)', 'bi-bar-chart-steps', '
                <p><em>Relevan untuk: Administrator (kelola), semua peran ber-izin <code>benchmarking.view</code> (lihat).</em></p>
                <p>Mencatat hasil perbandingan capaian mutu institusi/prodi dengan institusi lain, sebagai bahan penetapan target peningkatan.</p>
                <ol>
                    <li>Buka menu <strong>SPMI &gt; Benchmarking</strong>, klik <strong>+ Tambah Data</strong>.</li>
                    <li>Isi aspek yang dibandingkan (mis. "Rasio dosen:mahasiswa"), nama institusi pembanding, nilai milik sendiri, dan nilai institusi pembanding.</li>
                    ' . $ss('Form tambah benchmarking - aspek, institusi pembanding, nilai sendiri vs pembanding') . '
                    <li>Isi kesimpulan dan rekomendasi tindak lanjut berdasarkan hasil perbandingan tersebut.</li>
                </ol>
            '],
            'Survey Kepuasan' => ['SPMI (Siklus PPEPP)', 'bi-emoji-smile', '
                <p><em>Relevan untuk: Administrator.</em></p>
                <p>Merekap hasil survei kepuasan dari berbagai jenis responden (mahasiswa, dosen, tenaga kependidikan, alumni, pengguna lulusan). Survei sendiri dikumpulkan di luar sistem (mis. Google Form), lalu hasilnya direkap di sini.</p>
                <ol>
                    <li>Buka menu <strong>SPMI &gt; Survey Kepuasan</strong>, klik <strong>+ Tambah Hasil Survei</strong>.</li>
                    <li>Isi judul survei, jenis responden, jumlah responden, skala penilaian yang dipakai (mis. skala 1-4 atau 0-100), dan rata-rata skor hasil survei.</li>
                    ' . $ss('Form tambah hasil survei - jenis responden, jumlah responden, skala, rata-rata skor') . '
                    <li>Unggah berkas laporan lengkap survei (opsional) dan tulis ringkasan hasilnya.</li>
                    ' . $ss('Daftar survei kepuasan dengan badge skor dan persentase') . '
                </ol>
            '],
            'Monev' => ['SPMI (Siklus PPEPP)', 'bi-binoculars', '
                <p><em>Relevan untuk: Administrator, Kaprodi (kelola milik prodi sendiri).</em></p>
                <p>Monitoring dan evaluasi periodik atas aspek operasional non-audit - misalnya kehadiran dosen, kesesuaian pelaksanaan dengan RPS, atau ketersediaan sarana kelas.</p>
                <ol>
                    <li>Buka menu <strong>SPMI &gt; Monev</strong>, klik <strong>+ Tambah Monev</strong>.</li>
                    <li>Pilih prodi (atau kosongkan untuk aspek institusional), isi aspek yang dimonitor, tanggal pelaksanaan, dan petugas yang melakukan monev.</li>
                    ' . $ss('Form tambah monev - aspek, tanggal, petugas, hasil, status') . '
                    <li>Tuliskan hasil temuan monev dan tetapkan status (Baik/Cukup/Kurang); jika status bukan Baik, isi juga rencana tindak lanjutnya.</li>
                </ol>
            '],
            'Evaluasi Pembelajaran' => ['SPMI (Siklus PPEPP)', 'bi-easel', '
                <p><em>Relevan untuk: Kaprodi (kelola milik prodi sendiri), Administrator (kelola semua).</em></p>
                <p>Mengevaluasi pelaksanaan pembelajaran tiap mata kuliah per semester - apakah sudah sesuai dengan RPS yang direncanakan, kendala apa yang dihadapi, dan rekomendasi perbaikannya.</p>
                <ol>
                    <li>Buka menu <strong>SPMI &gt; Evaluasi Pembelajaran</strong>, klik <strong>+ Tambah Mata Kuliah</strong> untuk mendaftarkan mata kuliah yang akan dievaluasi semester ini.</li>
                    ' . $ss('Form tambah mata kuliah untuk evaluasi pembelajaran') . '
                    <li>Setelah semester berjalan, klik tombol <strong>Evaluasi</strong> pada baris mata kuliah tersebut.</li>
                    <li>Pada jendela yang muncul, pilih tingkat <strong>Kesesuaian dengan RPS</strong> (Sesuai/Kurang Sesuai/Tidak Sesuai), lalu isi kendala dan rekomendasi jika ada.</li>
                    ' . $ss('Jendela evaluasi pembelajaran - pilihan kesesuaian RPS, kendala, rekomendasi') . '
                </ol>
                <p>Status mata kuliah otomatis berubah dari "Belum" menjadi "Dievaluasi" begitu evaluasi disimpan, lengkap dengan pencatatan siapa dan kapan mengevaluasinya.</p>
            '],
            'Capaian Pembelajaran' => ['SPMI (Siklus PPEPP)', 'bi-graph-up-arrow', '
                <p><em>Relevan untuk: Kaprodi (kelola milik prodi sendiri), Administrator (kelola semua).</em></p>
                <p>Mencatat target dan realisasi capaian pembelajaran mata kuliah (CPMK) per tahun akademik.</p>
                <ol>
                    <li>Buka menu <strong>SPMI &gt; Capaian Pembelajaran</strong>, klik <strong>+ Tambah CPMK</strong>.</li>
                    <li>Isi mata kuliah, kode CPMK (mis. "CPMK-1"), deskripsi capaian yang diharapkan, dan <strong>Target Capaian</strong> dalam persen.</li>
                    ' . $ss('Form tambah CPMK - mata kuliah, kode CPMK, target capaian') . '
                    <li>Setelah nilai akhir semester direkap, edit kembali data ini dan isi <strong>Realisasi Capaian</strong>.</li>
                </ol>
                <p>Berbeda dari Sasaran Mutu, status di sini <strong>dihitung otomatis oleh sistem</strong> - begitu Realisasi diisi, sistem membandingkannya dengan Target: jika realisasi &ge; target maka berstatus <em>Tercapai</em>, jika di bawahnya maka <em>Tidak Tercapai</em>. Anda tidak perlu (dan tidak bisa) memilih status secara manual.</p>
                ' . $ss('Daftar capaian pembelajaran dengan badge status yang dihitung otomatis') . '
            '],
            'Review RPS' => ['SPMI (Siklus PPEPP)', 'bi-file-earmark-text', '
                <p><em>Relevan untuk: Kaprodi (kelola milik prodi sendiri), Administrator (kelola semua).</em></p>
                <p>Mencatat dan meninjau kelengkapan serta kesesuaian format Rencana Pembelajaran Semester (RPS) tiap mata kuliah.</p>
                <ol>
                    <li>Buka menu <strong>SPMI &gt; Review RPS</strong>, klik <strong>+ Tambah RPS</strong>.</li>
                    <li>Isi kode &amp; nama mata kuliah, dosen pengampu, SKS, semester/tahun akademik, dan unggah berkas RPS-nya.</li>
                    ' . $ss('Form tambah RPS - kode MK, mata kuliah, dosen, semester, unggah berkas') . '
                    <li>Reviewer (biasanya Kaprodi/LPM) klik tombol <strong>Review</strong> pada baris RPS yang ingin dinilai, lalu pilih status (Sesuai/Perlu Revisi/Belum Direview) dan isi catatan reviewer.</li>
                    ' . $ss('Jendela review RPS - pilihan status dan catatan reviewer') . '
                    <li>Jika dosen mengunggah ulang berkas RPS yang sudah direview sebelumnya, statusnya otomatis kembali ke <em>Belum Direview</em> karena perlu ditinjau ulang.</li>
                </ol>
                <p>Catatan reviewer <strong>wajib diisi</strong> saat memilih status "Perlu Revisi", supaya dosen tahu apa yang perlu diperbaiki.</p>
            '],
            'Aturan AMI' => ['SPMI (Siklus PPEPP)', 'bi-gear', '
                <p><em>Relevan untuk: Administrator.</em></p>
                <p>Berisi parameter umum yang dipakai di seluruh siklus AMI - bukan modul tersendiri, melainkan tab tambahan di halaman <strong>Pengaturan</strong> (lihat bab <strong>Pengaturan Situs</strong>).</p>
                <ol>
                    <li>Buka menu <strong>SPMI &gt; Aturan AMI</strong> (akan membuka halaman Pengaturan pada tab yang sesuai).</li>
                    ' . $ss('Tab Aturan AMI di halaman Pengaturan') . '
                    <li><strong>Pengingat Evaluasi Diri (hari)</strong> - berapa hari sebelum tanggal audit sistem mengirim pengingat "evaluasi diri belum dikirim" ke Kaprodi terkait (default 7 hari).</li>
                    <li><strong>Batas Waktu RTL Standar (hari)</strong> - acuan jumlah hari yang biasa dipakai saat mengisi tanggal batas tindak lanjut ketika mencatat temuan baru (default 14 hari).</li>
                    <li><strong>Nilai Minimum/Maksimum</strong> - rentang skala penilaian yang dipakai pada Evaluasi Diri dan Lembar Kerja Audit (default 1-4).</li>
                    <li><strong>Wajibkan Bukti Tindak Lanjut</strong> - jika diaktifkan, Kaprodi wajib melampirkan berkas bukti saat mengajukan RTL.</li>
                    <li><strong>Catatan/Instruksi Umum AMI</strong> - teks pengantar yang tampil di halaman Evaluasi Diri dan Lembar Kerja Audit.</li>
                    <li>Klik <strong>Simpan Pengaturan</strong> di bagian bawah untuk menerapkan perubahan.</li>
                </ol>
            '],

            // ---- Laporan (tambahan) ---------------------------------------------
            'Laporan AMI' => ['Laporan', 'bi-file-earmark-bar-graph', '
                <p><em>Relevan untuk: Administrator, Auditor, Viewer.</em></p>
                <p>Berbeda dari Export Laporan CSV (data mentah), Laporan AMI menyusun laporan naratif siap-cetak per periode AMI - ringkasan pelaksanaan, temuan, dan tindak lanjut dalam format laporan resmi.</p>
                ' . $ss('Halaman Laporan AMI - pilihan periode dan tombol unduh laporan') . '
                <p>Pilih periode AMI yang ingin dilaporkan, lalu unduh sebagai PDF untuk keperluan pelaporan ke pimpinan atau asesor eksternal.</p>
            '],

            // ---- Konten Publik — kategori baru -----------------------------------
            'Berita & Kategori Berita' => ['Konten Publik', 'bi-newspaper', '
                <p><em>Relevan untuk: Administrator, staf pengelola konten.</em></p>
                <p>Mengelola artikel berita yang tampil di halaman publik situs, dikelompokkan ke dalam kategori.</p>
                <ol>
                    <li>Buat kategori terlebih dahulu lewat menu <strong>Kategori Berita</strong> jika kategori yang dibutuhkan belum ada.</li>
                    <li>Buka menu <strong>Berita</strong>, klik <strong>+ Tambah Berita</strong>.</li>
                    ' . $ss('Form tambah berita - judul, kategori, gambar sampul, editor konten') . '
                    <li>Isi judul, pilih kategori, unggah gambar sampul, dan tulis isi berita di editor teks.</li>
                    <li>Simpan sebagai <strong>draft</strong> terlebih dahulu jika belum siap tayang, atau langsung publikasikan.</li>
                </ol>
            '],
            'Pengumuman & Agenda' => ['Konten Publik', 'bi-megaphone', '
                <p><em>Relevan untuk: Administrator, staf pengelola konten.</em></p>
                <p><strong>Pengumuman</strong> menampilkan informasi penting jangka pendek di halaman publik, sedangkan <strong>Agenda</strong> menampilkan jadwal kegiatan mendatang (dengan tanggal mulai/selesai).</p>
                ' . $ss('Halaman daftar pengumuman dan form tambah agenda dengan field tanggal') . '
                <p>Keduanya punya alur kerja yang sama seperti Berita: isi konten, tentukan tanggal berlaku/pelaksanaan, lalu publikasikan.</p>
            '],
            'Slider Beranda' => ['Konten Publik', 'bi-images', '
                <p><em>Relevan untuk: Administrator.</em></p>
                <p>Mengelola gambar-gambar besar (banner) yang bergantian tampil di bagian paling atas halaman beranda publik.</p>
                <ol>
                    <li>Buka menu <strong>Slider</strong>, klik <strong>+ Tambah Slide</strong>.</li>
                    <li>Unggah gambar, isi judul/teks overlay (opsional), dan tautan tujuan jika slide tersebut perlu bisa diklik.</li>
                    ' . $ss('Form tambah slide - unggah gambar, judul overlay, tautan tujuan') . '
                    <li>Atur urutan tampil antar slide, dan nonaktifkan slide yang sudah tidak relevan tanpa perlu menghapusnya.</li>
                </ol>
            '],
            'Pesan Masuk (Kontak)' => ['Konten Publik', 'bi-envelope', '
                <p><em>Relevan untuk: Administrator.</em></p>
                <p>Menampung pesan yang dikirim pengunjung lewat formulir kontak di halaman publik.</p>
                <ol>
                    <li>Buka menu <strong>Kontak</strong> untuk melihat daftar pesan masuk (pesan yang belum dibaca ditandai berbeda).</li>
                    ' . $ss('Daftar pesan masuk dengan penanda belum dibaca') . '
                    <li>Klik salah satu pesan untuk membaca isi lengkapnya, lalu klik <strong>Balas</strong> untuk mengirim balasan lewat email ke pengirim.</li>
                </ol>
            '],

            // ---- Administrasi Sistem (tambahan) ----------------------------------
            'Masuk sebagai Pengguna Lain (Impersonation)' => ['Administrasi Sistem', 'bi-person-badge', '
                <p><em>Relevan untuk: Administrator.</em></p>
                <p>Fitur ini memungkinkan Administrator masuk sementara sebagai akun pengguna lain - berguna untuk membantu troubleshooting saat pengguna melaporkan kendala yang sulit direproduksi lewat penjelasan saja.</p>
                <ol>
                    <li>Buka menu <strong>Pengguna</strong>, cari akun yang ingin diperiksa.</li>
                    <li>Klik tombol <strong>Login sebagai</strong> pada baris akun tersebut.</li>
                    ' . $ss('Tombol "Login sebagai" pada daftar pengguna, dan pita penanda mode impersonation di header') . '
                    <li>Sistem akan menampilkan pita/penanda khusus di bagian atas layar selama Anda berada dalam mode ini, mengingatkan bahwa Anda sedang login sebagai pengguna lain, bukan sebagai diri sendiri.</li>
                    <li>Klik <strong>Kembali ke Akun Saya</strong> pada pita tersebut untuk mengakhiri sesi impersonation dan kembali ke akun Administrator Anda.</li>
                </ol>
                <p>Gunakan fitur ini secukupnya dan hanya untuk kebutuhan dukungan teknis - setiap aksi yang dilakukan selama impersonation tetap tercatat di Audit Trail.</p>
            '],
            'Pengaturan Situs' => ['Administrasi Sistem', 'bi-gear-wide-connected', '
                <p><em>Relevan untuk: Administrator.</em></p>
                <p>Pusat pengaturan tampilan dan informasi dasar situs, diakses lewat menu <strong>Pengaturan</strong>. Terbagi dalam beberapa tab di sisi kiri halaman.</p>
                <ul>
                    <li><strong>Pengaturan Umum</strong> - nama situs, tagline, logo utama, logo kedua (opsional, mis. logo mitra/akreditasi), favicon, teks footer.</li>
                    ' . $ss('Halaman Pengaturan - tab Pengaturan Umum dengan form nama situs dan unggah logo') . '
                    <li><strong>Informasi Kontak</strong> - alamat, telepon, email yang tampil di halaman publik.</li>
                    <li><strong>Media Sosial</strong> - tautan akun media sosial resmi institusi.</li>
                    <li><strong>Login Demo</strong> - opsional, menampilkan tombol "Masuk sebagai Demo" di halaman login menggunakan akun contoh yang sudah ditentukan.</li>
                    <li><strong>Aturan AMI</strong> - parameter siklus AMI (lihat bab tersendiri di kategori SPMI).</li>
                    <li><strong>Pengaturan Template</strong> - opsi tampilan tambahan untuk halaman publik.</li>
                </ul>
                <p>Setiap tab punya tombol <strong>Simpan Pengaturan</strong> sendiri di bagian bawah - pastikan menyimpan tab yang sedang diedit sebelum berpindah ke tab lain.</p>
            '],
        ];

        // Urutan akhir yang diinginkan (existing + baru, lintas kategori).
        // Kategori baru disisipkan di posisi yang masuk akal dalam alur PPEPP,
        // bukan sekadar ditumpuk di akhir daftar.
        $order = [
            'Tentang Sistem Ini', 'Cara Login & Captcha', 'Mengenal Peran & Hak Akses',
            'Navigasi Dasar Admin Panel', 'Profil & Keamanan Akun Saya',

            'Dashboard Utama', 'Dashboard AMI',

            'Mengelola Data Program Studi', 'Mengelola Data Akreditasi',
            'Dashboard Akreditasi', 'Halaman Transparansi Akreditasi Publik',

            'Periode AMI', 'Data Auditor', 'Jadwal Audit & Penugasan',
            'Penugasan Saya (Auditor)', 'Standar Mutu', 'Instrumen Audit',
            'Evaluasi Diri (Auditee)', 'Lembar Kerja Audit (Auditor)',
            'Mencatat Temuan Audit', 'Tindak Lanjut Temuan', 'Verifikasi RTL',
            'Monitoring AMI', 'Dokumen/Bukti Audit', 'Rapat Tinjauan Manajemen (RTM)',
            'Analitik Mutu', 'Audit Trail',

            'Kebijakan, Manual & Formulir SPMI', 'Sasaran Mutu', 'Pengendalian Dokumen',
            'Rencana Peningkatan Mutu', 'Benchmarking', 'Survey Kepuasan', 'Monev',
            'Evaluasi Pembelajaran', 'Capaian Pembelajaran', 'Review RPS', 'Aturan AMI',

            'Jenis Dokumen', 'Upload & Alur Persetujuan Dokumen',
            'Riwayat Versi Dokumen', 'Dokumen Publik',

            'Export Laporan CSV', 'Laporan AMI',

            'Berita & Kategori Berita', 'Pengumuman & Agenda', 'Slider Beranda',
            'Pesan Masuk (Kontak)',

            'Manajemen Pengguna & Peran', 'Masuk sebagai Pengguna Lain (Impersonation)',
            'Editor Menu Website Publik', 'Pengaturan Situs',

            'Pertanyaan Umum (FAQ)',
        ];

        // "Manajemen Menu" mendeskripsikan fitur reorder sidebar admin yang sudah
        // dihapus total dari sistem - isinya diganti total dengan fitur yang
        // sebenarnya masih ada: editor drag-and-drop menu navbar WEBSITE PUBLIK.
        if ($old = Panduan::where('judul', 'Manajemen Menu')->first()) {
            $old->update([
                'judul' => 'Editor Menu Website Publik',
                'slug' => Str::slug('Editor Menu Website Publik'),
                'icon' => 'bi-list-nested',
                'konten' => '
                    <p><em>Relevan untuk: Administrator.</em></p>
                    <p>Menu navigasi di halaman depan (publik) - termasuk yang bercabang beberapa tingkat seperti Profil, SPMI, AMI, Dokumen, dst - dikelola lewat editor drag-and-drop tersendiri, terpisah dari menu sidebar admin (yang sekarang diatur lewat kode program, bukan lagi lewat antarmuka).</p>
                    <ol>
                        <li>Buka menu <strong>Pengaturan &gt; Menu Website</strong>.</li>
                        ' . $ss('Halaman Editor Menu Website - struktur pohon menu dengan tombol tambah/edit') . '
                        <li>Geser (drag) item menu untuk mengubah urutan tampil atau memindahkannya menjadi anak dari item lain - hingga maksimal 4 tingkat kedalaman.</li>
                        <li>Klik ikon pensil pada suatu item untuk quick-edit: nama, tujuan/tautan, dan apakah tautan tersebut perlu dibuka di <strong>tab baru</strong> (biasanya untuk tautan ke situs eksternal).</li>
                        ' . $ss('Panel quick-edit menu - field nama, tujuan/link, dan toggle buka tab baru') . '
                        <li>Klik <strong>+ Tambah Menu</strong> untuk menu baru, isi apakah menu tersebut berupa tautan langsung atau sekadar induk (dropdown) berisi sub-menu.</li>
                    </ol>
                    <p>Perubahan struktur menu langsung terlihat di halaman publik setelah disimpan.</p>
                ',
            ]);
        }

        foreach ($order as $index => $judul) {
            $urutan = $index + 1;

            if ($existing = Panduan::where('judul', $judul)->first()) {
                $existing->update(['urutan' => $urutan]);

                continue;
            }

            if (! isset($chapters[$judul])) {
                continue;
            }

            [$kategori, $icon, $konten] = $chapters[$judul];

            Panduan::create([
                'kategori' => $kategori,
                'judul' => $judul,
                'konten' => $konten,
                'icon' => $icon,
                'urutan' => $urutan,
                'is_active' => true,
            ]);
        }
    }

    public function down(): void
    {
        // Konten panduan - tidak ada rollback otomatis.
    }
};
