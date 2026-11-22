<?php

namespace App\Services\Siakad;

use App\Http\Controllers\Admin\DosenExcelController;
use App\Models\DkpsKualitasInput;
use App\Models\DkpsKurikulum;
use App\Models\DkpsSubmission;
use App\Models\Dosen;
use App\Models\MataKuliah;
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
    public function syncDosen(?Prodi $defaultProdi = null, ?string $emailDomain = null): array
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
        $hbRaw = array_map(fn ($r) => $r['HomeBase'] ?? null, $rows);
        $rows = array_map(function ($r) use ($hbKeys, $hbNorm) {
            $hb = $this->pick($r, $hbKeys);
            // HomeBase berupa objek/daftar, mis. {"KodeProgdi": "...", "NamaProgdi": "..."}
            if ($hb === '') {
                foreach ($r as $k => $v) {
                    if (is_array($v) && in_array($this->norm((string) $k), $hbNorm, true)) {
                        $inner = array_is_list($v) ? ($v[0] ?? []) : $v;
                        $hb = is_array($inner) ? $this->pick($inner, array_merge($hbKeys, ['Kode', 'Nama'])) : (is_scalar($inner) ? trim((string) $inner) : '');
                        if ($hb !== '') {
                            break;
                        }
                    }
                }
            }
            $r = array_filter($r, fn ($k) => ! in_array($this->norm((string) $k), $hbNorm, true), ARRAY_FILTER_USE_KEY);
            $r['HomeBase'] = $hb;

            return $r;
        }, $rows);

        // Prodi ditarik dulu: dipakai untuk pencarian HomeBase per prodi di bawah, dan agar
        // HomeBase dosen (kode/nama prodi SIAKAD) dikenali saat import.
        $prodi = $this->syncProdi();

        // HomeBase kosong di get_dosen: tanya SIAKAD per prodi (get_dosen + Filter kolom prodi).
        // Dosen yang sudah di-plot ke prodi di SIAKAD ikut terbawa prodinya.
        $kosong = array_filter($rows, fn ($r) => $r['HomeBase'] === '');
        if ($kosong) {
            $plot = $this->homebasePerProdi(count($rows));
            if ($plot) {
                $rows = array_map(function ($r) use ($plot) {
                    if ($r['HomeBase'] === '') {
                        $r['HomeBase'] = $plot[$this->dosenKey($r)['nidn']] ?? $plot[$this->dosenKey($r)['kode']] ?? '';
                    }

                    return $r;
                }, $rows);
            }
        }

        // Masih kosong: pakai prodi dosen yang sudah tercatat di SPMI, dicocokkan lewat
        // NIDN lalu Kode Dosen.
        $existing = Dosen::with('prodi:id,kode,kode_siakad')->get(['id', 'prodi_id', 'nidn_nidk', 'kode_dosen', 'email']);
        $byNidn = $existing->filter(fn ($d) => $d->nidn_nidk)->keyBy(fn ($d) => $this->cleanId($d->nidn_nidk));
        $byKode = $existing->filter(fn ($d) => $d->kode_dosen)->keyBy(fn ($d) => strtolower($this->cleanId($d->kode_dosen)));
        $emailDomain = $emailDomain ? ltrim(strtolower(trim($emailDomain)), '@') : null;
        $rows = array_map(function ($r) use ($byNidn, $byKode, $emailDomain) {
            $nidn = $this->cleanId($this->pick($r, ['NIDN', 'NIDK', 'NIDN_NIDK']));
            $kodeAsli = $this->cleanId($this->pick($r, ['KodeDosen', 'Kode']));
            $kode = strtolower($kodeAsli);
            $d = ($nidn !== '' ? $byNidn->get($nidn) : null) ?? ($kode !== '' ? $byKode->get($kode) : null);

            if ($r['HomeBase'] === '' && $d?->prodi) {
                $r['HomeBase'] = $d->prodi->kode;
            }

            // API tidak mengirim Email; SIAKAD memakai pola {KodeDosen}@domain. Hanya mengisi
            // email yang masih kosong di SPMI, tidak menimpa email yang sudah diisi.
            if ($emailDomain && $this->pick($r, ['Email', 'E-mail', 'Surel']) === '' && ! $d?->email
                && preg_match('/^[A-Za-z0-9._-]+$/', $kodeAsli)) {
                $r['Email'] = strtolower($kodeAsli).'@'.$emailDomain;
            }

            return $r;
        }, $rows);

        if (! $defaultProdi && ! collect($rows)->contains(fn ($r) => $r['HomeBase'] !== '')) {
            return ['status' => 'error', 'created' => 0, 'updated' => 0, 'skipped' => 0, 'warnings' => [], 'total' => count($rows), 'prodi' => $prodi,
                'message' => 'Data dosen dari SIAKAD tidak memuat kolom HomeBase/prodi yang terisi, jadi dosen tidak bisa dipetakan ke program studi.',
                'errors' => [
                    'Kolom yang dikirim SIAKAD: '.implode(', ', $kolomAsli).'.',
                    'Isi kolom HomeBase pada '.count($rows).' dosen: '.$this->describeValues($hbRaw).'.',
                    'Pencarian dosen per prodi ke SIAKAD (Filter '.implode('/', self::FILTER_PRODI).') juga tidak menemukan plot prodi.',
                    'Pilih "Prodi untuk dosen baru" saat menekan Tarik dari SIAKAD, atau minta pengelola SIAKAD mengisi kolom HomeBase pada data API dosen.',
                ]];
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

        $result = app(DosenExcelController::class)->importData($table, $defaultProdi, true);
        $result['total'] = count($rows);
        $result['prodi'] = $prodi;

        // Pesan import Excel diganti dengan petunjuk yang sesuai untuk SIAKAD.
        // Dosen baru tanpa HomeBase diringkas jadi satu baris (bisa ratusan).
        $tanpaHomebase = [];
        $result['errors'] = array_values(array_filter($result['errors'], function ($e) use (&$tanpaHomebase) {
            if (preg_match('/^Baris \d+ \((.*)\): Homebase kosong \(pilih "Homebase default" saat import\)$/', $e, $m)) {
                $tanpaHomebase[] = $m[1];

                return false;
            }

            return true;
        }));
        if ($tanpaHomebase) {
            array_unshift($result['errors'], count($tanpaHomebase).' dosen baru dilewati karena HomeBase-nya kosong di SIAKAD dan belum ada di SPMI: '
                .implode(', ', array_slice($tanpaHomebase, 0, 30)).(count($tanpaHomebase) > 30 ? ', dan '.(count($tanpaHomebase) - 30).' lainnya' : '')
                .'. Pilih "Prodi untuk dosen baru" lalu tarik ulang, atau tambahkan/import per prodi.');
        }

        $prodiDilewati = implode('; ', $prodi['skipped']);
        $result['errors'] = array_map(function ($e) use ($prodiDilewati) {
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

    /**
     * Mata kuliah kurikulum prodi -> DKPS Tabel Kurikulum. Dicocokkan lewat kode MK (lalu
     * nama MK); yang ada diperbarui semester/nama/SKS-nya, tautan RPS/asesmen tidak diubah.
     *
     * @return array{status:string,message:string,created:int,updated:int,skipped:array,total:int}
     */
    public function syncKurikulum(DkpsSubmission $dkp): array
    {
        if (! $dkp->prodi) {
            return $this->gagal('DKPS ini belum terhubung ke program studi.');
        }
        $ambil = $this->ambilMataKuliah($dkp->prodi);
        if ($ambil['status'] !== 'ok') {
            return $ambil;
        }
        [$rows, $sumber] = [$ambil['rows'], $ambil['sumber']];

        // DKPS memakai kurikulum yang berlaku: bila SIAKAD mengirim kolom Kurikulum, ambil yang terbaru saja.
        $terbaru = collect($rows)->map(fn ($r) => $this->bacaMk($r)['kurikulum'])->filter()
            ->sort(fn ($a, $b) => strnatcasecmp($b, $a))->first();
        if ($terbaru !== null) {
            $rows = array_values(array_filter($rows, fn ($r) => $this->bacaMk($r)['kurikulum'] === $terbaru));
            $sumber .= ", kurikulum {$terbaru}";
        }

        $existing = DkpsKurikulum::where('dkps_submission_id', $dkp->id)->get();
        $byKode = $existing->filter(fn ($k) => $k->kode_mk)->keyBy(fn ($k) => strtolower(trim($k->kode_mk)));
        $byNama = $existing->keyBy(fn ($k) => $this->norm($k->nama_mk));
        $urutan = (int) $existing->max('urutan');
        $created = 0;
        $updated = 0;
        $skipped = [];
        $seen = [];

        DB::transaction(function () use ($rows, $dkp, $byKode, $byNama, &$urutan, &$created, &$updated, &$skipped, &$seen) {
            foreach ($rows as $r) {
                ['kode' => $kode, 'nama' => $nama, 'semester' => $semRaw] = $this->bacaMk($r);
                $label = $nama !== '' ? $nama : ($kode !== '' ? $kode : '(tanpa nama)');

                if ($nama === '') {
                    continue; // baris bukan mata kuliah
                }
                $kunciKode = $kode !== '' ? 'k:'.strtolower($kode) : null;
                $kunciNama = 'n:'.$this->norm($nama);
                if (($kunciKode && isset($seen[$kunciKode])) || isset($seen[$kunciNama])) {
                    continue; // MK yang sama muncul lebih dari sekali (mis. beberapa kelas/dosen)
                }
                $seen[$kunciNama] = true;
                if ($kunciKode) {
                    $seen[$kunciKode] = true;
                }

                $semester = $this->romawi($semRaw);
                $model = ($kode !== '' ? $byKode->get(strtolower($kode)) : null) ?? $byNama->get($this->norm($nama));
                if (! $semester && ! $model) {
                    $skipped[] = "{$label}: semester \"{$semRaw}\" tidak dikenali (harus 1-8)";

                    continue;
                }

                ['sks' => $sksTotal, 'sks_praktikum' => $sksPrak, 'sks_lapangan' => $sksLap, 'sks_teori' => $sksTeori] = $this->bacaMk($r);
                if ($sksTeori === null && $sksTotal !== null) {
                    $sksTeori = max(0, $sksTotal - (float) $sksPrak - (float) $sksLap);
                }

                $attrs = array_filter([
                    'kode_mk' => $kode !== '' ? mb_substr($kode, 0, 50) : null,
                    'nama_mk' => mb_substr($nama, 0, 255),
                    'semester' => $semester,
                    'sks_kuliah' => $sksTeori,
                    'sks_praktikum' => $sksPrak,
                    'sks_praktik_lapangan' => $sksLap,
                ], fn ($v) => $v !== null);

                if ($model) {
                    $model->update($attrs);
                    $updated++;
                } else {
                    DkpsKurikulum::create($attrs + ['dkps_submission_id' => $dkp->id, 'kompetensi_inti' => false, 'urutan' => ++$urutan]);
                    $created++;
                }
            }
        });

        Cache::forever('siakad:last_sync:kurikulum', now()->toDateTimeString());

        return ['status' => 'ok', 'message' => "sumber {$sumber}", 'created' => $created, 'updated' => $updated, 'skipped' => $skipped, 'total' => count($rows)];
    }

    /**
     * Mahasiswa prodi -> DKPS Tabel Kualitas Input (jumlah mahasiswa baru per angkatan
     * TS-4..TS, dan mahasiswa aktif untuk TS). Daya tampung, pendaftar, lulus seleksi
     * tidak ada di SIAKAD sehingga tidak diubah.
     *
     * @return array{status:string,message:string,created:int,updated:int,skipped:array,total:int}
     */
    public function syncMahasiswa(DkpsSubmission $dkp): array
    {
        $prodi = $dkp->prodi;
        $kodeProdi = $prodi?->kode_siakad ?: $prodi?->kode;
        if (! $kodeProdi || ! $dkp->tahun_ts_awal) {
            return $this->gagal('Prodi DKPS ini belum punya kode SIAKAD atau tahun TS belum diisi.');
        }

        try {
            $rows = $this->client->fetchAll('get_mahasiswa', "KodeProgdi='".str_replace("'", "''", $kodeProdi)."'");
        } catch (SiakadException $e) {
            // Filter ditolak: ambil semua lalu saring sendiri.
            $rows = $this->client->fetchAll('get_mahasiswa');
        }
        if (! $rows) {
            return $this->gagal("SIAKAD tidak mengembalikan data mahasiswa untuk prodi {$prodi->nama}.");
        }
        $kolom = array_keys($rows[0]);
        if ($this->pick($rows[0], self::KOLOM_PRODI) === '' && count($rows) > 0) {
            return $this->gagal('Data mahasiswa SIAKAD tidak memuat kolom prodi, jadi tidak bisa dipastikan milik prodi ini.', ['Kolom yang dikirim SIAKAD: '.implode(', ', $kolom)]);
        }
        $rows = $this->hanyaProdi($rows, $prodi);

        $baru = [];
        $aktif = ['reguler' => 0, 'transfer' => 0];
        $tanpaAngkatan = 0;
        foreach ($rows as $r) {
            $jalur = $this->pick($r, ['JalurMasuk', 'StatusMasuk', 'JenisPendaftaran', 'JenisMasuk', 'JenisDaftar', 'jenis_daftar', 'StatusAwal']);
            $jenis = preg_match('/pindah|transfer|alih|lanjut/i', $jalur) ? 'transfer' : 'reguler';

            $angkatanRaw = $this->pick($r, ['Angkatan', 'TahunMasuk', 'TahunAngkatan', 'ThnAngkatan', 'tahun_masuk', 'SemesterMasuk', 'PeriodeMasuk', 'TahunAkademikMasuk', 'TglMasuk', 'TanggalMasuk']);
            if (preg_match('/(19|20)\d{2}/', $angkatanRaw, $m)) {
                $baru[(int) $m[0]][$jenis] = ($baru[(int) $m[0]][$jenis] ?? 0) + 1;
            } else {
                $tanpaAngkatan++;
            }

            $status = $this->pick($r, ['StatusMahasiswa', 'StatusAktif', 'StatusAkademik', 'status_mahasiswa', 'Status', 'StatusMhs']);
            if (preg_match('/^(a|aktif|active|1|y)$/i', $status) || (stripos($status, 'aktif') !== false && ! preg_match('/tidak|non/i', $status))) {
                $aktif[$jenis]++;
            }
        }
        if (! $baru) {
            return $this->gagal('Kolom angkatan/tahun masuk mahasiswa tidak terbaca.', ['Kolom yang dikirim SIAKAD: '.implode(', ', $kolom)]);
        }

        $updated = 0;
        $created = 0;
        foreach (DkpsKualitasInput::TAHUN_RELATIF as $tahun) {
            $selisih = $tahun === 'TS' ? 0 : (int) substr($tahun, 3);
            $th = (int) $dkp->tahun_ts_awal - $selisih; // TS 2026/2027 = angkatan 2026
            $attrs = [
                'mahasiswa_baru_reguler' => $baru[$th]['reguler'] ?? 0,
                'mahasiswa_baru_transfer' => $baru[$th]['transfer'] ?? 0,
            ];
            if ($tahun === 'TS') {
                $attrs['mahasiswa_aktif_reguler'] = $aktif['reguler'];
                $attrs['mahasiswa_aktif_transfer'] = $aktif['transfer'];
            }
            $row = DkpsKualitasInput::firstOrNew(['dkps_submission_id' => $dkp->id, 'tahun_relatif' => $tahun]);
            $row->exists ? $updated++ : $created++;
            $row->fill($attrs)->save();
        }

        Cache::forever('siakad:last_sync:mahasiswa', now()->toDateTimeString());

        $skipped = $tanpaAngkatan ? ["{$tanpaAngkatan} mahasiswa tanpa angkatan/tahun masuk tidak dihitung sebagai mahasiswa baru"] : [];

        return ['status' => 'ok', 'message' => '', 'created' => $created, 'updated' => $updated, 'skipped' => $skipped, 'total' => count($rows)];
    }

    /**
     * Daftar mata kuliah per prodi -> tabel mata_kuliah (pilihan Mata Kuliah di Capaian
     * Pembelajaran, Evaluasi Pembelajaran, Review RPS). Tanpa $prodi: semua prodi yang
     * terhubung SIAKAD. Dicocokkan lewat kode MK lalu nama; MK yang hilang di SIAKAD tidak dihapus.
     *
     * @return array{status:string,message:string,created:int,updated:int,skipped:array,total:int}
     */
    public function syncMataKuliah(?Prodi $prodi = null): array
    {
        $prodis = $prodi ? collect([$prodi]) : Prodi::whereNotNull('kode_siakad')->where('kode_siakad', '!=', '')->get();
        if ($prodis->isEmpty()) {
            return $this->gagal('Belum ada prodi yang terhubung SIAKAD. Tarik data Program Studi dulu.');
        }

        $created = 0;
        $updated = 0;
        $total = 0;
        $skipped = [];
        foreach ($prodis as $p) {
            $ambil = $this->ambilMataKuliah($p);
            if ($ambil['status'] !== 'ok') {
                $skipped[] = "{$p->jenjang} {$p->nama}: {$ambil['message']}";
                array_push($skipped, ...array_slice($ambil['skipped'], 0, 3));

                continue;
            }
            $total += count($ambil['rows']);

            $existing = MataKuliah::where('prodi_id', $p->id)->get();
            $byKode = $existing->filter(fn ($m) => $m->kode_mk)->keyBy(fn ($m) => strtolower(trim($m->kode_mk)));
            $byNama = $existing->keyBy(fn ($m) => $this->norm($m->nama));
            $seen = [];
            $tanpaNama = 0;

            DB::transaction(function () use ($ambil, $p, $byKode, $byNama, &$seen, &$created, &$updated, &$tanpaNama) {
                foreach ($ambil['rows'] as $r) {
                    $mk = $this->bacaMk($r);
                    if ($mk['nama'] === '') {
                        $tanpaNama++;

                        continue;
                    }
                    // Satu MK bisa muncul berkali-kali (beberapa kelas/dosen/kurikulum): ambil sekali per kode & nama.
                    $kunciKode = $mk['kode'] !== '' ? 'k:'.strtolower($mk['kode']) : null;
                    $kunciNama = 'n:'.$this->norm($mk['nama']);
                    if (($kunciKode && isset($seen[$kunciKode])) || isset($seen[$kunciNama])) {
                        continue;
                    }
                    $seen[$kunciNama] = true;
                    if ($kunciKode) {
                        $seen[$kunciKode] = true;
                    }

                    $sem = $this->romawi($mk['semester']);
                    $attrs = array_filter([
                        'kode_mk' => $mk['kode'] !== '' ? mb_substr($mk['kode'], 0, 50) : null,
                        'nama' => mb_substr($mk['nama'], 0, 255),
                        'semester' => $sem ? array_search($sem, ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII'], true) : null,
                        'sks' => $mk['sks'] ?? (($mk['sks_teori'] ?? 0) + ($mk['sks_praktikum'] ?? 0) + ($mk['sks_lapangan'] ?? 0) ?: null),
                    ], fn ($v) => $v !== null);

                    $model = ($mk['kode'] !== '' ? $byKode->get(strtolower($mk['kode'])) : null) ?? $byNama->get($this->norm($mk['nama']));
                    if ($model) {
                        $model->update($attrs);
                        $updated++;
                    } else {
                        MataKuliah::create($attrs + ['prodi_id' => $p->id]);
                        $created++;
                    }
                }
            });
            if ($tanpaNama) {
                $skipped[] = "{$p->jenjang} {$p->nama}: {$tanpaNama} baris tanpa nama mata kuliah dilewati";
            }
        }

        if ($total === 0) {
            return $this->gagal('SIAKAD tidak mengembalikan mata kuliah.', $skipped);
        }
        Cache::forever('siakad:last_sync:matakuliah', now()->toDateTimeString());

        return ['status' => 'ok', 'message' => '', 'created' => $created, 'updated' => $updated, 'skipped' => $skipped, 'total' => $total];
    }

    // ---------------------------------------------------------------------

    /**
     * Ambil mata kuliah satu prodi dari SIAKAD. Dokumentasi: get_kurikulum memakai parameter
     * KodeProgdi; get_matakuliah lewat Filter. Dicoba berurutan sampai ada data.
     *
     * @return array{status:string,message:string,rows:array,sumber:?string,skipped:array,created:int,updated:int,total:int}
     */
    private function ambilMataKuliah(Prodi $prodi): array
    {
        $kodeProdi = $prodi->kode_siakad ?: $prodi->kode;
        if (! $kodeProdi) {
            return $this->gagal("Prodi {$prodi->nama} belum punya kode SIAKAD. Tarik data Program Studi dulu.") + ['rows' => [], 'sumber' => null];
        }

        $filter = "KodeProgdi='".str_replace("'", "''", $kodeProdi)."'";
        // get_matakuliah berisi daftar MK (KodeMatkul, NamaMatkul, ...). get_kurikulum di SIAKAD
        // ternyata berisi daftar kurikulum (nama_kurikulum, semester_mulai_berlaku), bukan MK,
        // jadi hanya dipakai bila barisnya memuat nama mata kuliah.
        $percobaan = [
            ['get_matakuliah', $filter, []],
            ['get_matakuliah', '', ['KodeProgdi' => $kodeProdi]],
            ['get_kurikulum', '', ['KodeProgdi' => $kodeProdi]],
            ['get_kurikulum', $filter, []],
        ];
        $pesanGagal = [];
        foreach ($percobaan as [$act, $f, $extra]) {
            try {
                $rows = $this->hanyaProdi($this->client->fetchAll($act, $f, null, $extra), $prodi);
            } catch (SiakadException $e) {
                $pesanGagal[] = $e->getMessage();

                continue;
            }
            if ($rows && ! collect($rows)->contains(fn ($r) => $this->bacaMk($r)['nama'] !== '')) {
                $pesanGagal[] = "{$act} tidak berisi nama mata kuliah (kolom: ".implode(', ', array_keys($rows[0])).')';

                continue;
            }
            if ($rows) {
                // Kurikulum terbaru lebih dulu, agar MK kembar (beberapa kurikulum/kelas) memakai data terbaru.
                usort($rows, fn ($a, $b) => strnatcasecmp($this->bacaMk($b)['kurikulum'], $this->bacaMk($a)['kurikulum']));

                return ['status' => 'ok', 'message' => '', 'rows' => $rows, 'sumber' => $act, 'skipped' => [], 'created' => 0, 'updated' => 0, 'total' => count($rows)];
            }
        }

        return $this->gagal("SIAKAD tidak mengembalikan mata kuliah untuk prodi {$prodi->nama} (KodeProgdi {$kodeProdi}).", array_unique($pesanGagal)) + ['rows' => [], 'sumber' => null];
    }

    /** Kolom mata kuliah dari SIAKAD (nama kolom belum terdokumentasi, dikenali lewat alternatif). */
    private function bacaMk(array $r): array
    {
        return [
            // SIAKAD get_matakuliah: ID, KodeMatkul, NamaMatkul, SKS, Semester, Kurikulum, KodeDosen, NamaDosen, KodeProgdi, NamaProgdi, ...
            'kode' => $this->pick($r, ['KodeMatkul', 'KodeMK', 'KodeMatakuliah', 'KodeMataKuliah', 'kode_mk', 'kode_matkul', 'KodeMtk']),
            'nama' => $this->pick($r, ['NamaMatkul', 'NamaMK', 'NamaMatakuliah', 'NamaMataKuliah', 'nama_mk', 'nama_matkul', 'Matakuliah', 'MataKuliah', 'NamaMtk']),
            'kurikulum' => $this->pick($r, ['Kurikulum', 'TahunKurikulum', 'KodeKurikulum', 'nama_kurikulum']),
            'semester' => $this->pick($r, ['Semester', 'SemesterKe', 'Smt', 'SemesterMK', 'semester', 'SemesterPaket']),
            'sks' => $this->angka($this->pick($r, ['SKS', 'JumlahSKS', 'TotalSKS', 'SKSMK', 'sks'])),
            'sks_praktikum' => $this->angka($this->pick($r, ['SKSPraktikum', 'SksPraktikum', 'SKS_Praktikum', 'SKSPrak', 'sks_praktikum'])),
            'sks_lapangan' => $this->angka($this->pick($r, ['SKSPraktikLapangan', 'SKSLapangan', 'SKSPL', 'SksLapangan', 'sks_lapangan'])),
            'sks_teori' => $this->angka($this->pick($r, ['SKSTeori', 'SksTeori', 'SKS_Teori', 'SKSKuliah', 'SKSTatapMuka', 'sks_teori'])),
        ];
    }

    private const KOLOM_PRODI = ['KodeProgdi', 'KodeProdi', 'kode_prodi', 'HomeBase', 'NamaProgdi', 'NamaProdi', 'Progdi', 'Prodi', 'ProgramStudi', 'nama_program_studi'];

    /** Saring baris milik $prodi bila baris memuat kolom prodi; tanpa kolom prodi, dibiarkan. */
    private function hanyaProdi(array $rows, Prodi $prodi): array
    {
        $cocok = array_filter(array_map(fn ($v) => $this->norm((string) $v), [$prodi->kode_siakad, $prodi->kode, $prodi->nama, $prodi->jenjang.' '.$prodi->nama]));

        return array_values(array_filter($rows, function ($r) use ($cocok) {
            $nilai = [];
            foreach (self::KOLOM_PRODI as $k) {
                $v = $this->pick($r, [$k]);
                if ($v !== '') {
                    $nilai[] = $this->norm($v);
                }
            }

            return ! $nilai || array_intersect($nilai, $cocok);
        }));
    }

    private function gagal(string $message, array $skipped = []): array
    {
        return ['status' => 'error', 'message' => $message, 'created' => 0, 'updated' => 0, 'skipped' => array_values($skipped), 'total' => 0];
    }

    /** "3", "03", "Semester 3", "III" -> "III" (1-8). */
    private function romawi(string $s): ?string
    {
        $romawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII'];
        $s = strtoupper(trim($s));
        if (in_array($s, $romawi, true)) {
            return $s;
        }
        if (preg_match('/\d+/', $s, $m) && (int) $m[0] >= 1 && (int) $m[0] <= 8) {
            return $romawi[(int) $m[0] - 1];
        }

        return null;
    }

    private function angka(string $s): ?float
    {
        $s = str_replace(',', '.', trim($s));

        return is_numeric($s) ? (float) $s : null;
    }

    /** Kolom prodi yang dicoba sebagai Filter get_dosen (nama kolom tidak terdokumentasi). */
    private const FILTER_PRODI = ['HomeBase', 'KodeProgdi', 'KodeProdi', 'KodeHomeBase', 'kode_prodi'];

    /**
     * Petakan dosen -> kode prodi SIAKAD dengan memanggil get_dosen per prodi memakai
     * Filter "<kolom>='<KodeProgdi>'". Kolom filter yang berhasil diingat untuk sinkron
     * berikutnya. Filter yang diabaikan server (mengembalikan semua dosen) tidak dipakai.
     *
     * @return array<string, string> "nidn:..."/"kode:..." => kode_siakad prodi
     */
    private function homebasePerProdi(int $totalDosen): array
    {
        $kodeProdi = Prodi::whereNotNull('kode_siakad')->where('kode_siakad', '!=', '')->pluck('kode_siakad')->all();
        if (! $kodeProdi) {
            return [];
        }

        $remembered = Cache::get('siakad:dosen_prodi_filter');
        $kolomList = $remembered && in_array($remembered, self::FILTER_PRODI, true)
            ? array_values(array_unique(array_merge([$remembered], self::FILTER_PRODI)))
            : self::FILTER_PRODI;

        foreach ($kolomList as $kolom) {
            $map = [];
            $valid = true;
            foreach ($kodeProdi as $kode) {
                try {
                    $list = $this->client->fetchAll('get_dosen', $kolom."='".str_replace("'", "''", $kode)."'");
                } catch (SiakadException) {
                    $valid = false; // kolom filter tidak dikenal server

                    break;
                }
                // Filter diabaikan (semua dosen kembali) -> kolom ini tidak bisa dipakai.
                if (count($kodeProdi) > 1 && $totalDosen > 1 && count($list) >= $totalDosen) {
                    $valid = false;

                    break;
                }
                foreach ($list as $d) {
                    foreach ($this->dosenKey($d) as $k) {
                        if ($k !== null) {
                            $map[$k] ??= $kode;
                        }
                    }
                }
            }

            if ($valid && $map) {
                Cache::forever('siakad:dosen_prodi_filter', $kolom);

                return $map;
            }
        }

        return [];
    }

    /** @return array{nidn:?string,kode:?string} kunci pencocokan dosen */
    private function dosenKey(array $r): array
    {
        $nidn = $this->cleanId($this->pick($r, ['NIDN', 'NIDK', 'NIDN_NIDK']));
        $kode = strtolower($this->cleanId($this->pick($r, ['KodeDosen', 'Kode'])));

        return ['nidn' => $nidn !== '' ? 'nidn:'.$nidn : null, 'kode' => $kode !== '' ? 'kode:'.$kode : null];
    }

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

    /** Ringkasan jenis isi (tanpa membocorkan data), mis. "120 kosong, 3 null". */
    private function describeValues(array $values): string
    {
        $jenis = [];
        foreach ($values as $v) {
            $label = match (true) {
                $v === null => 'null/tidak ada',
                is_array($v) => 'objek/daftar ('.implode(',', array_keys($v)).')',
                is_bool($v) => 'boolean',
                trim((string) $v) === '' => 'kosong',
                default => 'terisi',
            };
            $jenis[$label] = ($jenis[$label] ?? 0) + 1;
        }

        return collect($jenis)->map(fn ($n, $l) => "{$n} {$l}")->join(', ');
    }

    /** NIDN/kode dari SIAKAD kadang diawali tanda kutip (format teks Excel). */
    private function cleanId(?string $s): string
    {
        return trim(ltrim(trim((string) $s), "'"));
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
