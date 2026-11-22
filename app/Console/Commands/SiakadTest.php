<?php

namespace App\Console\Commands;

use App\Services\Siakad\SiakadClient;
use App\Services\Siakad\SiakadException;
use Illuminate\Console\Command;

/**
 * Cek koneksi API SIAKAD & lihat NAMA KOLOM yang dikirim tiap layanan.
 * Isi data disamarkan, jadi hasilnya aman dikirim ke pengembang untuk pemetaan kolom.
 */
class SiakadTest extends Command
{
    protected $signature = 'siakad:test
        {act=get_token : get_token | get_fakultas | get_prodi | get_dosen | get_mahasiswa | get_kurikulum | get_matakuliah}
        {--filter= : Filter SIAKAD, mis. "KodeFakultas=\'FEB\'"}
        {--progdi= : Parameter KodeProgdi (wajib untuk get_kurikulum)}
        {--limit=3 : Jumlah contoh baris}
        {--raw : Tampilkan isi asli (JANGAN dibagikan, berisi data pribadi)}
        {--probe : Pindai nama layanan lain (RPS, mata kuliah, jadwal, ...) yang mungkin tersedia}';

    /** Nama layanan yang dicoba oleh --probe (tidak terdokumentasi; ditebak dari pola get_*). */
    private const PROBE_ACTS = [
        'get_rps', 'get_rps_matakuliah', 'get_rencana_pembelajaran', 'get_matakuliah', 'get_mata_kuliah', 'get_mk',
        'get_kurikulum', 'get_matakuliah_kurikulum', 'get_jadwal', 'get_jadwal_kuliah', 'get_kelas', 'get_kelas_kuliah',
        'get_pengampu', 'get_dosen_pengampu', 'get_ajar', 'get_mengajar', 'get_tahun_akademik', 'get_semester',
        'get_periode', 'get_krs', 'get_nilai', 'get_cpl', 'get_cpmk', 'get_lulusan', 'get_wisuda', 'get_kaprodi',
    ];

    protected $description = 'Tes koneksi API SIAKAD dan tampilkan nama kolom + contoh data (disamarkan)';

    public function handle(SiakadClient $client): int
    {
        $act = $this->argument('act');
        $this->line('URL     : '.config('services.siakad.url'));
        $this->line('Username: '.(config('services.siakad.username') ? config('services.siakad.username') : '(belum diisi di .env)'));

        try {
            $token = $client->token();
            $this->info('Token OK ('.strlen($token).' karakter).');
            if ($this->option('probe')) {
                return $this->probe($client, $token);
            }
            if ($act === 'get_token') {
                return self::SUCCESS;
            }

            $extra = $this->option('progdi') ? ['KodeProgdi' => (string) $this->option('progdi')] : [];
            $rows = $client->fetchAll($act, (string) $this->option('filter'), max(1, (int) $this->option('limit')), $extra);
        } catch (SiakadException $e) {
            $this->error($e->getMessage());
            if ($e->siakadCode === '05') {
                $this->warn('Semua format Filter/Limit sudah dicoba dan ditolak. Tanyakan ke pengelola SIAKAD contoh Filter & Limit yang benar untuk "'.$act.'".');
            }

            return self::FAILURE;
        }

        if ($client->variantUsed !== null) {
            $v = $client::VARIANTS[$client->variantUsed];
            $this->line('Format diterima: Filter="'.$v['filter'].'", Limit='.($v['base'] === null ? '(tanpa Limit)' : '"'.$v['base'].'-..."'));
        }

        if (! $rows) {
            $this->warn("{$act}: tidak ada data.");

            return self::SUCCESS;
        }

        $this->info("{$act}: nama kolom = ".implode(', ', array_keys($rows[0])));
        foreach ($rows as $i => $row) {
            $this->line('--- contoh '.($i + 1));
            foreach ($row as $k => $v) {
                $val = is_scalar($v) || $v === null ? (string) $v : json_encode($v);
                $this->line(str_pad($k, 24).': '.($this->option('raw') ? $val : $this->mask($k, $val)));
            }
        }
        if (! $this->option('raw')) {
            $this->comment('Isi data disamarkan. Hasil ini aman dikirim ke pengembang.');
        }

        return self::SUCCESS;
    }

    private function probe(SiakadClient $client, string $token): int
    {
        $this->line('Memindai '.count(self::PROBE_ACTS).' nama layanan...');
        $found = [];
        foreach (self::PROBE_ACTS as $act) {
            $status = null;
            foreach ([['Filter' => '-', 'Limit' => '0-2'], ['Filter' => '', 'Limit' => '1-2'], ['Filter' => '-']] as $params) {
                try {
                    $rows = $client->call($act, ['Token' => $token] + $params);
                    $status = $rows ? 'ADA  kolom: '.implode(', ', array_keys($rows[0])) : 'ADA  (data kosong)';
                    $found[] = $act;
                    break;
                } catch (SiakadException $e) {
                    $status = 'tidak: '.preg_replace('/^SIAKAD menolak permintaan "[^"]*": /', '', $e->getMessage());
                    if ($e->siakadCode !== '05') {
                        break;
                    }
                }
            }
            str_starts_with($status, 'ADA') ? $this->info(str_pad($act, 26).$status) : $this->line(str_pad($act, 26).$status);
        }

        $this->newLine();
        $found
            ? $this->info('Layanan yang tersedia: '.implode(', ', $found).'. Lihat isinya: php artisan siakad:test '.$found[0])
            : $this->warn('Tidak ada layanan tambahan yang ditemukan. Tanyakan nama layanan (Act) RPS/mata kuliah ke pengelola SIAKAD.');
        $this->comment('Hasil ini tidak berisi data pribadi dan aman dikirim ke pengembang.');

        return self::SUCCESS;
    }

    /** Samarkan isi, tapi biarkan bentuknya terlihat (panjang, angka/huruf, format tanggal). */
    private function mask(string $key, string $val): string
    {
        if ($val === '') {
            return '(kosong)';
        }
        // Kode/jenjang/jabatan/status pendek tidak bersifat pribadi & penting untuk pemetaan.
        if (preg_match('/kode|jenjang|strata|jabatan|golongan|status|agama|jenis|kelamin|aktif|fakultas|prodi|homebase|pendidikan/i', $key) && mb_strlen($val) <= 40 && ! str_contains($val, '@')) {
            return $val;
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $val)) {
            return substr($val, 0, 4).'-xx-xx (format tanggal Y-m-d)';
        }

        return preg_replace(['/[A-Za-z]/', '/\d/'], ['x', '9'], mb_substr($val, 0, 3)).str_repeat('*', max(0, min(12, mb_strlen($val) - 3)))." (panjang ".mb_strlen($val).')';
    }
}
