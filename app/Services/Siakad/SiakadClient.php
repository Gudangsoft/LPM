<?php

namespace App\Services\Siakad;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Klien API SIAKAD (wslive/apidata).
 *
 * Semua permintaan: POST form-data dengan Username, Password, Datetime, Act, Filter
 * (+ Token & Limit untuk data). Respons: {status, error_code, message, data: [...]}.
 * Format ini sudah dicek langsung ke server: JSON body ditolak ("unknown_request").
 */
class SiakadClient
{
    private ?string $token = null;

    public function __construct(
        private ?string $url = null,
        private ?string $username = null,
        private ?string $password = null,
    ) {
        $this->url ??= config('services.siakad.url');
        $this->username ??= config('services.siakad.username');
        $this->password ??= config('services.siakad.password');
    }

    public function isConfigured(): bool
    {
        return filled($this->url) && filled($this->username) && filled($this->password);
    }

    public function token(): string
    {
        if ($this->token) {
            return $this->token;
        }
        $data = $this->call('get_token');
        $token = $data[0]['Token'] ?? $data[0]['token'] ?? null;
        if (! $token) {
            throw new SiakadException('SIAKAD tidak mengembalikan token.');
        }

        return $this->token = $token;
    }

    /**
     * Format parameter yang dicoba berurutan bila SIAKAD membalas kode 05
     * ("Invalid parameter or filter format"). Dokumentasi menulis "Filter : -" dan
     * "Limit : 10-1000" tanpa penjelasan, jadi format yang diterima dicari lalu diingat.
     * filter = nilai Filter saat tanpa filter; base = angka awal Limit (null = tanpa Limit).
     */
    public const VARIANTS = [
        ['filter' => '-', 'base' => 0],
        ['filter' => '-', 'base' => 1],
        ['filter' => '', 'base' => 1],
        ['filter' => '', 'base' => 0],
        ['filter' => '-', 'base' => null],
        ['filter' => '', 'base' => null],
    ];

    /** Format yang terakhir berhasil (indeks VARIANTS), untuk ditampilkan siakad:test. */
    public ?int $variantUsed = null;

    /**
     * Ambil semua baris untuk $act (get_fakultas, get_prodi, get_dosen, get_mahasiswa).
     * $filter contoh: "KodeFakultas='FEB'".
     *
     * Arti "Limit" (mis. "10-1000") tidak dijelaskan dokumentasi; dikirim "awal-akhir" per
     * halaman. Baris kembar dibuang, dan berhenti bila halaman tidak menambah data baru,
     * sehingga aman baik bila maknanya "dari-sampai" maupun "mulai-jumlah".
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchAll(string $act, string $filter = '', ?int $maxRows = null): array
    {
        $remembered = Cache::get('siakad:param_variant');
        $order = array_keys(self::VARIANTS);
        if (is_int($remembered) && isset(self::VARIANTS[$remembered])) {
            $order = array_values(array_unique(array_merge([$remembered], $order)));
        }

        $lastError = null;
        foreach ($order as $i) {
            try {
                $rows = $this->fetchWithVariant($act, $filter, $maxRows, self::VARIANTS[$i]);
            } catch (SiakadException $e) {
                if ($e->siakadCode !== '05') {
                    throw $e;
                }
                $lastError = $e;
                continue;
            }
            $this->variantUsed = $i;
            if ($remembered !== $i) {
                Cache::forever('siakad:param_variant', $i);
            }

            return $rows;
        }

        throw $lastError;
    }

    private function fetchWithVariant(string $act, string $filter, ?int $maxRows, array $variant): array
    {
        $pageSize = max(10, (int) config('services.siakad.page_size', 1000));
        $filter = trim($filter) === '' ? $variant['filter'] : $filter;
        $rows = [];
        $seen = [];

        for ($page = 0, $offset = 0; $page < 50; $page++, $offset += $pageSize) {
            $params = ['Token' => $this->token(), 'Filter' => $filter];
            if ($variant['base'] !== null) {
                $start = $offset + $variant['base'];
                $params['Limit'] = $start.'-'.($start + $pageSize - $variant['base']);
            }
            $batch = $this->call($act, $params);

            if ($variant['base'] === null) {
                return $maxRows ? array_slice($batch, 0, $maxRows) : $batch;
            }

            $new = 0;
            foreach ($batch as $row) {
                $key = md5(json_encode($row));
                if (! isset($seen[$key])) {
                    $seen[$key] = true;
                    $rows[] = $row;
                    $new++;
                }
            }

            if ($new === 0 || count($batch) < $pageSize || ($maxRows && count($rows) >= $maxRows)) {
                break;
            }
        }

        return $maxRows ? array_slice($rows, 0, $maxRows) : $rows;
    }

    /** @return array<int, array<string, mixed>> isi "data" */
    public function call(string $act, array $params = []): array
    {
        if (! $this->isConfigured()) {
            throw new SiakadException('Koneksi SIAKAD belum diatur. Isi SIAKAD_API_USERNAME dan SIAKAD_API_PASSWORD di file .env server.');
        }

        try {
            $response = Http::asForm()
                ->timeout((int) config('services.siakad.timeout', 60))
                ->acceptJson()
                ->post($this->url, array_merge([
                    'Username' => $this->username,
                    'Password' => $this->password,
                    'Datetime' => now()->format('Y-m-d H:i:s'),
                    'Act' => $act,
                    'Filter' => '',
                ], $params));
        } catch (\Throwable $e) {
            throw new SiakadException('Tidak dapat terhubung ke SIAKAD: '.$e->getMessage(), previous: $e);
        }

        $json = $response->json();
        if (! is_array($json)) {
            throw new SiakadException("Respons SIAKAD tidak dikenali (HTTP {$response->status()}).");
        }

        if (strtolower((string) ($json['status'] ?? '')) !== 'ok') {
            $msg = $json['message'] ?? 'tanpa pesan';
            // Data kosong kadang dilaporkan sebagai error "0 record"/"not found".
            if (preg_match('/^0 record|not ?found|tidak ada data|no data/i', (string) $msg)) {
                return [];
            }
            $e = new SiakadException("SIAKAD menolak permintaan \"{$act}\": {$msg}".(! empty($json['error_code']) ? " (kode {$json['error_code']})" : ''));
            $e->siakadCode = isset($json['error_code']) ? (string) $json['error_code'] : null;
            throw $e;
        }

        $data = $json['data'] ?? [];

        return is_array($data) ? array_values(array_filter($data, 'is_array')) : [];
    }
}
