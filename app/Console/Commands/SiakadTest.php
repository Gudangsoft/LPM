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
        {act=get_token : get_token | get_fakultas | get_prodi | get_dosen | get_mahasiswa}
        {--filter= : Filter SIAKAD, mis. "KodeFakultas=\'FEB\'"}
        {--limit=3 : Jumlah contoh baris}
        {--raw : Tampilkan isi asli (JANGAN dibagikan, berisi data pribadi)}';

    protected $description = 'Tes koneksi API SIAKAD dan tampilkan nama kolom + contoh data (disamarkan)';

    public function handle(SiakadClient $client): int
    {
        $act = $this->argument('act');
        $this->line('URL     : '.config('services.siakad.url'));
        $this->line('Username: '.(config('services.siakad.username') ? config('services.siakad.username') : '(belum diisi di .env)'));

        try {
            $token = $client->token();
            $this->info('Token OK ('.strlen($token).' karakter).');
            if ($act === 'get_token') {
                return self::SUCCESS;
            }

            $rows = $client->fetchAll($act, (string) $this->option('filter'), max(1, (int) $this->option('limit')));
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
