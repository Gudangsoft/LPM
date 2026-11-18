<?php

namespace App\Services\Siakad;

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
        $pageSize = max(10, (int) config('services.siakad.page_size', 1000));
        $rows = [];
        $seen = [];

        for ($page = 0, $offset = 0; $page < 50; $page++, $offset += $pageSize) {
            $batch = $this->call($act, [
                'Token' => $this->token(),
                'Filter' => $filter,
                'Limit' => $offset.'-'.($offset + $pageSize),
            ]);

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
            throw new SiakadException("SIAKAD menolak permintaan \"{$act}\": {$msg}".(! empty($json['error_code']) ? " (kode {$json['error_code']})" : ''));
        }

        $data = $json['data'] ?? [];

        return is_array($data) ? array_values(array_filter($data, 'is_array')) : [];
    }
}
