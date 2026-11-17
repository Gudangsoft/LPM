<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Mengosongkan data transaksi/dummy sebelum sistem dipakai untuk data real.
 *
 * DIPERTAHANKAN:
 *  - Data sistem: menus, roles, permissions, role_permission, pengaturan, panduan, migrations
 *  - Master data: prodi, standar_mutu, butir_instrumen, jenis_dokumen, kategori_berita,
 *    struktur_organisasi, halaman
 *  - Akun ber-role admin
 *
 * DIKOSONGKAN: semua tabel di $tables di bawah, plus akun non-admin.
 */
class ResetSpmiData extends Command
{
    protected $signature = 'spmi:reset-data
        {--force : Lewati konfirmasi (untuk skrip otomatis)}
        {--hapus-file : Hapus juga file upload (berita, dokumen, galeri, slider, DKPS, temuan)}';

    protected $description = 'Kosongkan data transaksi/dummy SPMI & konten website; master data, data sistem, dan akun admin dipertahankan';

    /** Tabel yang dikosongkan seluruhnya. */
    private array $tables = [
        // DKPS
        'dkps_submissions', 'dkps_beban_kerja_dtps', 'dkps_integrasi_penelitian_pkm', 'dkps_ipk_lulusan',
        'dkps_karya_inovatif_mahasiswa', 'dkps_kegiatan_luar_kelas', 'dkps_kepuasan_mahasiswa',
        'dkps_kepuasan_pengguna_kemampuan', 'dkps_kepuasan_pengguna_referensi', 'dkps_kerjasama',
        'dkps_kesesuaian_bidang', 'dkps_kualitas_input', 'dkps_kurikulum', 'dkps_lulusan_bekerja',
        'dkps_masa_studi_lulusan', 'dkps_pembimbingan_magang', 'dkps_pembimbingan_ta',
        'dkps_penelitian_pkm_mahasiswa', 'dkps_penelitian_pkm_ringkasan', 'dkps_pengembangan_kompetensi',
        'dkps_penggunaan_dana', 'dkps_prasarana', 'dkps_prestasi_mahasiswa', 'dkps_publikasi_dtps',
        'dkps_publikasi_dtps_detail', 'dkps_rekognisi_dtps', 'dkps_sarana_lab', 'dkps_sitasi_dtps',
        'dkps_tenaga_kependidikan_summary', 'dkps_tik', 'dkps_waktu_tunggu',
        'dosen_tetap', 'tenaga_kependidikan',

        // AMI, RTM & modul mutu
        'periode_ami', 'jadwal_ami', 'penugasan_ami', 'auditor', 'audit_butir', 'bukti_audit',
        'evaluasi_diri', 'temuan_ami', 'tindak_lanjut', 'rtm', 'rtm_agenda', 'rtm_keputusan',
        'monev', 'evaluasi_pembelajaran', 'rps_review', 'sasaran_mutu', 'survey_kepuasan',
        'benchmarking', 'capaian_pembelajaran', 'akreditasi', 'statistik',

        // Konten website
        'berita', 'pengumuman', 'agenda', 'galeri', 'dokumen', 'dokumen_versions', 'sliders', 'kontak',

        // Log, notifikasi, sesi & antrean
        'notifications', 'audit_trails', 'visitors', 'sessions', 'cache', 'cache_locks',
        'jobs', 'job_batches', 'failed_jobs', 'password_reset_tokens',
    ];

    /** Folder di disk "public" yang berisi upload milik tabel-tabel di atas. */
    private array $uploadDirs = ['berita', 'dkps', 'dokumen', 'galeri', 'sliders', 'temuan'];

    public function handle(): int
    {
        $tables = array_values(array_filter($this->tables, fn ($t) => Schema::hasTable($t)));
        $adminIds = $this->adminUserIds();

        if ($adminIds->isEmpty()) {
            $this->error('Tidak ditemukan akun admin. Dibatalkan agar Anda tidak terkunci dari sistem.');

            return self::FAILURE;
        }

        $this->warn('Database: '.DB::connection()->getDatabaseName());
        $this->table(['Tabel', 'Jumlah baris'], collect($tables)
            ->map(fn ($t) => [$t, DB::table($t)->count()])
            ->filter(fn ($row) => $row[1] > 0)
            ->values()
            ->all());

        $usersToDelete = DB::table('users')->whereNotIn('id', $adminIds)->pluck('name');
        $this->line('Akun admin yang dipertahankan: '.DB::table('users')->whereIn('id', $adminIds)->pluck('name')->implode(', '));
        $this->line('Akun yang akan dihapus ('.$usersToDelete->count().'): '.($usersToDelete->implode(', ') ?: '-'));
        if ($this->option('hapus-file')) {
            $this->line('Folder upload yang akan dihapus: storage/app/public/{'.implode(',', $this->uploadDirs).'}');
        }

        $this->newLine();
        $this->error(' PERINGATAN: data di atas akan DIHAPUS PERMANEN. Pastikan sudah backup database. ');

        if (! $this->option('force')
            && $this->ask('Ketik HAPUS untuk melanjutkan') !== 'HAPUS') {
            $this->info('Dibatalkan. Tidak ada data yang dihapus.');

            return self::SUCCESS;
        }

        Schema::disableForeignKeyConstraints();
        try {
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }

            DB::table('prodi')->whereNotIn('kaprodi_id', $adminIds)->update(['kaprodi_id' => null]);
            DB::table('role_user')->whereNotIn('user_id', $adminIds)->delete();
            DB::table('users')->whereNotIn('id', $adminIds)->delete();
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        if ($this->option('hapus-file')) {
            foreach ($this->uploadDirs as $dir) {
                Storage::disk('public')->deleteDirectory($dir);
            }
        }

        $this->info('Selesai. Data transaksi dikosongkan; master data, data sistem, dan akun admin tetap ada.');
        $this->line('Jalankan: php artisan optimize:clear');

        return self::SUCCESS;
    }

    /** Akun dengan kolom role=admin atau memiliki role "admin" di tabel roles. */
    private function adminUserIds()
    {
        $roleAdminIds = DB::table('role_user')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where(fn ($q) => $q->where('roles.slug', 'admin')->orWhere('roles.name', 'Administrator'))
            ->pluck('role_user.user_id');

        return DB::table('users')
            ->where('role', 'admin')
            ->orWhereIn('id', $roleAdminIds)
            ->pluck('id')
            ->unique()
            ->values();
    }
}
