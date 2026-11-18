<?php

namespace App\Services\Siakad;

use App\Http\Controllers\Admin\DosenExcelController;
use App\Models\Prodi;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Tarik data master dari SIAKAD ke SPMI.
 *
 * Nama kolom di respons API belum terdokumentasi, jadi dikenali lewat beberapa nama
 * alternatif (KodeProdi/Kode/..., NamaProdi/Nama/...). Gunakan `php artisan siakad:test
 * get_prodi` untuk melihat nama kolom sebenarnya bila ada yang tidak terbaca.
 */
class SiakadSync
{
    public function __construct(private SiakadClient $client)
    {
    }

    /**
     * Prodi: cocokkan lewat kode SIAKAD -> kode SPMI -> nama+jenjang.
     * Prodi baru ditambahkan; yang ada diperbarui nama/jenjang/fakultas-nya.
     * Kode SPMI yang sudah ada dan kaprodi tidak diubah.
     *
     * @return array{created:int,updated:int,skipped:array,total:int}
     */
    public function syncProdi(): array
    {
        $fakultas = [];
        foreach ($this->client->fetchAll('get_fakultas') as $f) {
            $kode = $this->pick($f, ['KodeFakultas', 'Kode', 'kode_fakultas']);
            $nama = $this->pick($f, ['NamaFakultas', 'Nama', 'nama_fakultas']);
            if ($kode !== '') {
                $fakultas[strtolower($kode)] = $nama;
            }
        }

        $rows = $this->client->fetchAll('get_prodi');
        $created = 0;
        $updated = 0;
        $skipped = [];

        DB::transaction(function () use ($rows, $fakultas, &$created, &$updated, &$skipped) {
            foreach ($rows as $r) {
                // SIAKAD memakai "Progdi" (KodeProgdi, NamaProgdi, nama_jenjang_pendidikan).
                $kode = $this->pick($r, ['KodeProgdi', 'KodeProdi', 'Kode', 'KodeProgramStudi', 'kode_prodi', 'KodeProdiDikti', 'KodeDikti']);
                $nama = $this->pick($r, ['NamaProgdi', 'NamaProdi', 'NamaProgramStudi', 'Nama', 'ProgramStudi', 'nama_prodi', 'Prodi', 'Progdi']);
                $jenjangRaw = $this->pick($r, ['nama_jenjang_pendidikan', 'Jenjang', 'JenjangProdi', 'JenjangStudi', 'Strata', 'KodeJenjang', 'jenjang', 'JenjangPendidikan']);
                $kodeFak = $this->pick($r, ['KodeFakultas', 'kode_fakultas', 'Fakultas']);
                $namaFak = $this->pick($r, ['NamaFakultas', 'nama_fakultas']) ?: ($fakultas[strtolower($kodeFak)] ?? $kodeFak);

                [$jenjang, $namaBersih] = $this->normalizeJenjang($jenjangRaw, $nama);
                if ($kode === '' || $namaBersih === '') {
                    $skipped[] = 'Kode/nama prodi tidak terbaca (kolom dari SIAKAD: '.implode(', ', array_keys($r)).')';

                    continue;
                }
                if (! $jenjang) {
                    $skipped[] = "{$namaBersih} ({$kode}): jenjang \"{$jenjangRaw}\" tidak dikenali (hanya D3/D4/S1/S2/S3)";

                    continue;
                }

                $prodi = Prodi::where('kode_siakad', $kode)->first()
                    ?? Prodi::whereNull('kode_siakad')->where('kode', $kode)->first()
                    ?? Prodi::whereNull('kode_siakad')->where('jenjang', $jenjang)->get()
                        ->first(fn ($p) => $this->norm($p->nama) === $this->norm($namaBersih));

                $attrs = ['kode_siakad' => $kode, 'nama' => $namaBersih, 'jenjang' => $jenjang];
                if ($namaFak !== '') {
                    $attrs['fakultas'] = $namaFak;
                }

                if ($prodi) {
                    $prodi->update($attrs);
                    $updated++;
                } else {
                    $attrs['kode'] = Prodi::where('kode', $kode)->exists() ? "{$kode}-SIAKAD" : $kode;
                    $attrs['is_active'] = true;
                    Prodi::create($attrs);
                    $created++;
                }
            }
        });

        Cache::forever('siakad:last_sync:prodi', now()->toDateTimeString());

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped, 'total' => count($rows)];
    }

    /**
     * Dosen: respons API diubah jadi tabel (judul = nama kolom API) lalu diproses
     * mesin import Excel Data Dosen (pengenal kolom SIAKAD, pencocokan HomeBase,
     * NIDN/Kode Dosen, sel kosong tidak menimpa data lama). Baris yang salah dilewati.
     *
     * @return array{status:string,message:string,created:int,updated:int,skipped:int,errors:array,warnings:array,total:int}
     */
    public function syncDosen(): array
    {
        $rows = $this->client->fetchAll('get_dosen');
        if (! $rows) {
            return ['status' => 'error', 'message' => 'SIAKAD tidak mengembalikan data dosen.', 'created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => [], 'warnings' => [], 'total' => 0];
        }

        // HomeBase bisa bernama lain di API (KodeProgdi, NamaProgdi, ...). Satukan jadi satu
        // kolom "HomeBase": kode prodi diutamakan, lalu nama prodi.
        $hbKeys = ['HomeBase', 'Home_Base', 'KodeHomeBase', 'KodeProgdi', 'KodeProdi', 'kode_prodi', 'KodeProgramStudi',
            'NamaProgdi', 'NamaProdi', 'nama_prodi', 'Progdi', 'Prodi', 'ProgramStudi', 'NamaProgramStudi'];
        $hbNorm = array_map(fn ($k) => $this->norm($k), $hbKeys);
        $kolomAsli = array_keys($rows[0]);
        $rows = array_map(function ($r) use ($hbKeys, $hbNorm) {
            $hb = $this->pick($r, $hbKeys);
            $r = array_filter($r, fn ($k) => ! in_array($this->norm((string) $k), $hbNorm, true), ARRAY_FILTER_USE_KEY);
            $r['HomeBase'] = $hb;

            return $r;
        }, $rows);

        if (! collect($rows)->contains(fn ($r) => $r['HomeBase'] !== '')) {
            return ['status' => 'error', 'created' => 0, 'updated' => 0, 'skipped' => 0, 'warnings' => [], 'total' => count($rows),
                'message' => 'Data dosen dari SIAKAD tidak memuat kolom HomeBase/prodi yang terisi, jadi dosen tidak bisa dipetakan ke program studi.',
                'errors' => ['Kolom yang dikirim SIAKAD: '.implode(', ', $kolomAsli).'. Kirim daftar ini ke pengembang.']];
        }

        $headers = [];
        foreach ($rows as $r) {
            foreach (array_keys($r) as $k) {
                $headers[$k] = true;
            }
        }
        $headers = array_keys($headers);
        $table = [$headers];
        foreach ($rows as $r) {
            $table[] = array_map(fn ($h) => is_scalar($r[$h] ?? null) ? (string) $r[$h] : '', $headers);
        }

        // Prodi ditarik dulu agar HomeBase dosen (kode/nama prodi SIAKAD) dikenali.
        $prodi = $this->syncProdi();

        $result = app(DosenExcelController::class)->importData($table, null, true);
        $result['total'] = count($rows);
        $result['prodi'] = $prodi;

        // Pesan import Excel diganti dengan petunjuk yang sesuai untuk SIAKAD.
        $prodiDilewati = implode('; ', $prodi['skipped']);
        $result['errors'] = array_map(function ($e) use ($prodiDilewati) {
            $e = str_replace('Homebase kosong (pilih "Homebase default" saat import)', 'HomeBase kosong di SIAKAD. Lengkapi di SIAKAD lalu tarik ulang, atau tambahkan dosen ini secara manual', $e);

            return preg_replace_callback('/Homebase "([^"]*)" tidak dikenal atau bukan prodi Anda/', function ($m) use ($prodiDilewati) {
                $alasan = $prodiDilewati !== '' && stripos($prodiDilewati, $m[1]) !== false
                    ? ' (prodi ini dilewati saat sinkron, lihat alasannya di atas)'
                    : '';

                return "Prodi \"{$m[1]}\" belum ada di menu Program Studi SPMI{$alasan}. Tambahkan prodinya lalu tarik ulang";
            }, $e);
        }, $result['errors']);

        if ($result['status'] === 'ok') {
            Cache::forever('siakad:last_sync:dosen', now()->toDateTimeString());
        }

        return $result;
    }

    // ---------------------------------------------------------------------

    /** Ambil nilai kolom pertama yang ada (tanpa peduli huruf besar/kecil & tanda baca). */
    private function pick(array $row, array $keys): string
    {
        $byNorm = [];
        foreach ($row as $k => $v) {
            $byNorm[$this->norm((string) $k)] = $v;
        }
        foreach ($keys as $k) {
            $v = $byNorm[$this->norm($k)] ?? null;
            if (is_scalar($v) && trim((string) $v) !== '') {
                return trim((string) $v);
            }
        }

        return '';
    }

    private function norm(string $s): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($s));
    }

    /**
     * Jenjang dari kolom jenjang (S1, Sarjana, kode PDDikti C/B/A/E/D) atau dari awalan
     * nama ("S1 Teknik Informatika"). Mengembalikan [jenjang, nama tanpa awalan jenjang].
     */
    private function normalizeJenjang(string $raw, string $nama): array
    {
        $map = [
            'd3' => 'D3', 'diploma3' => 'D3', 'diii' => 'D3', 'e' => 'D3',
            'd4' => 'D4', 'diploma4' => 'D4', 'div' => 'D4', 'sarjanaterapan' => 'D4', 'strs' => 'D4', 'd' => 'D4',
            's1' => 'S1', 'sarjana' => 'S1', 'c' => 'S1',
            's2' => 'S2', 'magister' => 'S2', 'master' => 'S2', 'b' => 'S2',
            's3' => 'S3', 'doktor' => 'S3', 'doktoral' => 'S3', 'a' => 'S3',
        ];
        $jenjang = $map[$this->norm($raw)] ?? null;

        $namaBersih = trim($nama);
        if (preg_match('/^\s*(D[\-\s]?3|D[\-\s]?4|S[\-\s]?1|S[\-\s]?2|S[\-\s]?3)\b[\s\-.:]*(.+)$/i', $namaBersih, $m)) {
            $jenjang ??= strtoupper(str_replace(['-', ' '], '', $m[1]));
            $namaBersih = trim($m[2]);
        }

        // Kolom jenjang tidak dikirim/kosong: anggap Sarjana (S1), jenjang terbanyak.
        if (! $jenjang && trim($raw) === '') {
            $jenjang = 'S1';
        }

        return [$jenjang, $namaBersih];
    }
}
