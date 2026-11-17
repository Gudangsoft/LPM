<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export / import Data Dosen lewat Excel, kompatibel dengan ekspor SIAKAD.
 *
 * Import membaca kolom berdasarkan JUDUL (bukan urutan), dengan banyak nama
 * alternatif (lihat ALIASES), sehingga file SIAKAD bisa diunggah langsung.
 * Kolom gabungan ala SIAKAD ("Kode / NIDN", "Nama & Kontak", "Alamat & Email",
 * "Golongan / Jabatan") juga dipecah otomatis.
 *
 * Pencocokan data lama: NIDN/NIDK -> Kode Dosen -> Nama + Homebase.
 * Seluruh file divalidasi dulu; bila ada satu baris salah, tidak ada yang disimpan.
 */
class DosenExcelController extends Controller implements HasMiddleware
{
    /** Kolom template/export: field => judul kolom. */
    private const COLUMNS = [
        'kode_dosen' => 'Kode Dosen',
        'nidn_nidk' => 'NIDN/NIDK',
        'nuptk' => 'NUPTK',
        'nama' => 'Nama Dosen',
        'jenis_kelamin' => 'Jenis Kelamin',
        'no_hp' => 'No. HP',
        'email' => 'Email',
        'alamat' => 'Alamat',
        'homebase' => 'Homebase',
        'pendidikan_terakhir' => 'Pendidikan Terakhir',
        'lulusan' => 'Lulusan',
        'pendidikan_magister_bidang' => 'Pendidikan S2 (Bidang)',
        'pendidikan_doktor_bidang' => 'Pendidikan S3 (Bidang)',
        'bidang_keahlian' => 'Bidang Keahlian',
        'golongan' => 'Golongan',
        'jabatan_akademik' => 'Jabatan Akademik',
        'no_sertifikat_pendidik' => 'No. Sertifikat Pendidik',
        'aktif' => 'Aktif',
    ];

    /**
     * Judul kolom yang dikenali (sudah dinormalisasi: huruf kecil, hanya a-z0-9).
     * Kunci berawalan "_" adalah kolom gabungan yang dipecah di splitCombined().
     */
    private const ALIASES = [
        'kode_dosen' => ['kodedosen', 'kode', 'iddosen'],
        'nidn_nidk' => ['nidnnidk', 'nidn', 'nidk', 'nidnnidknup', 'nup'],
        'nuptk' => ['nuptk'],
        'nama' => ['namadosen', 'nama', 'namalengkap', 'namagelar', 'namadengangelar'],
        'jenis_kelamin' => ['jeniskelamin', 'jnskel', 'jk', 'kelamin', 'gender', 'lp'],
        'no_hp' => ['nohp', 'hp', 'nohandphone', 'handphone', 'telepon', 'telp', 'notelp', 'notelepon', 'nohptelp', 'kontak', 'wa', 'nowa'],
        'email' => ['email', 'surel', 'alamatemail'],
        'alamat' => ['alamat', 'alamatrumah', 'alamatlengkap', 'domisili'],
        'homebase' => ['homebase', 'homebasekodeprodi', 'kodeprodi', 'prodi', 'programstudi', 'namaprodi', 'prodihomebase'],
        'pendidikan_terakhir' => ['pendidikanterakhir', 'pendidikan', 'jenjangpendidikan', 'pendidikantertinggi'],
        'lulusan' => ['lulusan', 'lulusanperguruantinggi', 'perguruantinggi', 'almamater', 'asalpt', 'asalperguruantinggi', 'universitas'],
        'pendidikan_magister_bidang' => ['pendidikans2bidang', 'pendidikanmagisters2bidang', 'pendidikanmagister', 'bidangs2', 'magister', 's2'],
        'pendidikan_doktor_bidang' => ['pendidikans3bidang', 'pendidikandoktors3bidang', 'pendidikandoktor', 'bidangs3', 'doktor', 's3'],
        'bidang_keahlian' => ['bidangkeahlian', 'keahlian', 'bidangilmu', 'kepakaran'],
        'golongan' => ['golongan', 'gol', 'pangkatgolongan', 'golonganpangkat', 'golpangkat', 'pangkat'],
        'jabatan_akademik' => ['jabatanakademik', 'jabatanfungsional', 'jabfung', 'jabatan', 'jafung'],
        'no_sertifikat_pendidik' => ['nosertifikatpendidik', 'sertifikatpendidik', 'serdos', 'nosertifikasidosen', 'sertifikasidosen', 'noserdos'],
        'aktif' => ['aktif', 'status', 'statusaktif', 'statusdosen'],
        '_kode_nidn' => ['kodenidn'],
        '_nama_kontak' => ['namakontak'],
        '_alamat_email' => ['alamatemail2', 'alamatdanemail', 'alamatemailkontak'],
        '_golongan_jabatan' => ['golonganjabatan', 'goljabatan', 'pangkatjabatan'],
    ];

    public static function middleware(): array
    {
        return [
            new Middleware('permission:dosen.view', only: ['export']),
            new Middleware('permission:dosen.manage', only: ['template', 'import']),
        ];
    }

    public function export(Request $request)
    {
        $rows = Dosen::with('prodi')
            ->visibleTo(auth()->user())
            ->when($request->filled('prodi_id'), fn ($q) => $q->where('prodi_id', $request->prodi_id))
            ->orderBy('prodi_id')->orderBy('nama')->get()
            ->map(fn (Dosen $d) => [
                $d->kode_dosen, $d->nidn_nidk, $d->nuptk, $d->nama, $d->jenis_kelamin,
                $d->no_hp, $d->email, $d->alamat, $d->prodi?->kode,
                $d->pendidikan_terakhir, $d->lulusan,
                $d->pendidikan_magister_bidang, $d->pendidikan_doktor_bidang, $d->bidang_keahlian,
                $d->golongan, Dosen::JABATAN[$d->jabatan_akademik] ?? null, $d->no_sertifikat_pendidik,
                $d->is_active ? 'Ya' : 'Tidak',
            ])->all();

        return $this->download($this->buildSpreadsheet($rows), 'data-dosen-'.now()->format('Ymd-His').'.xlsx');
    }

    public function template()
    {
        $kodeContoh = $this->allowedProdis()->first()?->kode ?? 'TI';
        $spreadsheet = $this->buildSpreadsheet([[
            'D001', '0612345678', '', 'Dr. Nama Dosen, M.Kom.', 'L', '081234567890', 'dosen@unukase.ac.id',
            'Jl. Contoh No. 1, Kebumen', $kodeContoh, 'S3', 'Universitas Gadjah Mada',
            'Ilmu Komputer', 'Ilmu Komputer', 'Rekayasa Perangkat Lunak', 'III/c', 'Lektor', '', 'Ya',
        ]]);

        $info = $spreadsheet->createSheet();
        $info->setTitle('Petunjuk');
        $info->fromArray([
            ['PETUNJUK PENGISIAN'],
            [''],
            ['Nama Dosen', 'WAJIB. Lengkap dengan gelar.'],
            ['Homebase', 'WAJIB. Kode prodi (lihat daftar di bawah) atau nama prodi, misalnya "Teknik Informatika".'],
            ['NIDN/NIDK', 'Disarankan. Dipakai untuk mencocokkan data lama (lalu Kode Dosen, lalu Nama + Homebase).'],
            ['Jenis Kelamin', 'L / P (atau Laki-laki / Perempuan).'],
            ['Pendidikan Terakhir', implode(' / ', Dosen::PENDIDIKAN)],
            ['Jabatan Akademik', implode(' / ', Dosen::JABATAN)],
            ['Golongan', 'Bebas, misalnya III/b.'],
            ['Aktif', 'Ya / Tidak (atau status SIAKAD: Aktif, Cuti, Pensiun, Keluar, ...). Dikosongkan = Ya.'],
            [''],
            ['IMPORT DARI SIAKAD', 'File ekspor SIAKAD bisa langsung diunggah. Kolom dibaca berdasarkan judul kolom (urutan bebas),'],
            ['', 'kolom yang tidak dikenal diabaikan. Kolom gabungan seperti "Kode / NIDN", "Nama & Kontak",'],
            ['', '"Alamat & Email", dan "Golongan / Jabatan" dipecah otomatis.'],
            [''],
            ['Kode Prodi', 'Nama Program Studi'],
        ]);
        $info->fromArray($this->allowedProdis()->map(fn ($p) => [$p->kode, "{$p->jenjang} {$p->nama}"])->all(), null, 'A17');
        $info->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $info->getStyle('A3:A12')->getFont()->setBold(true);
        $info->getStyle('A16:B16')->getFont()->setBold(true);
        $info->getColumnDimension('A')->setWidth(22);
        $info->getColumnDimension('B')->setWidth(110);
        $info->getStyle('A:A')->getNumberFormat()->setFormatCode('@');
        $spreadsheet->setActiveSheetIndex(0);

        return $this->download($spreadsheet, 'template-import-dosen.xlsx');
    }

    public function import(Request $request)
    {
        // Sengaja tidak memakai aturan "mimes": banyak SIAKAD mengekspor ".xls" yang isinya
        // tabel HTML (mime text/html), sehingga akan tertolak. Isi file diperiksa saat dibaca.
        $request->validate([
            'file' => 'required|file|max:10240',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $defaultProdi = $request->filled('prodi_default')
            ? $this->allowedProdis()->firstWhere('id', (int) $request->prodi_default)
            : null;
        if ($request->filled('prodi_default') && ! $defaultProdi) {
            return back()->with('error', 'Homebase default tidak valid.');
        }

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['xlsx', 'xls', 'csv', 'ods', 'htm', 'html', 'txt'], true)) {
            return back()->with('error', 'Format file harus Excel (.xlsx/.xls), .csv, atau .ods.');
        }

        $spreadsheet = $this->loadSpreadsheet($file->getRealPath(), $ext);
        if (! $spreadsheet) {
            return back()->with('error', 'File tidak dapat dibaca. Bila file berasal dari SIAKAD, coba buka di Excel lalu "Save As" ke format .xlsx, kemudian unggah ulang.');
        }

        // Cari sheet & baris judul (ekspor SIAKAD kadang diawali judul laporan / ada di sheet lain).
        $data = [];
        $map = null;
        $headerRow = 0;
        $firstHeaders = [];
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $sheetData = $sheet->toArray(null, true, true, false);
            [$headerRow, $map] = $this->detectHeader($sheetData, $defaultProdi !== null);
            if ($map !== null) {
                $data = $sheetData;
                break;
            }
            $firstHeaders = $firstHeaders ?: $this->firstNonEmptyRow($sheetData);
        }
        if ($map === null) {
            return back()
                ->with('error', 'Kolom "Nama" dan "Homebase/Prodi" tidak ditemukan di file. Bila file memang tidak punya kolom Homebase, pilih "Homebase default" saat import. Bila masih gagal, kirimkan daftar judul kolom di bawah ke pengelola sistem.')
                ->with('import_errors', ['Judul kolom yang terbaca: '.($firstHeaders ? implode(' | ', $firstHeaders) : '(kosong)')]);
        }

        $prodiLookup = $this->prodiLookup();
        $allowedProdiIds = $this->allowedProdis()->pluck('id')->all();
        $jabatanByLabel = collect(Dosen::JABATAN)->mapWithKeys(fn ($label, $key) => [$this->norm($label) => $key, $this->norm($key) => $key]);

        $errors = [];
        $rows = [];
        $seen = [];

        foreach (array_slice($data, $headerRow + 1, null, true) as $idx => $raw) {
            $line = $idx + 1;
            $v = $this->extractRow($raw, $map);
            if (implode('', array_diff_key($v, ['_present' => 1])) === '') {
                continue;
            }

            $e = [];

            if ($v['homebase'] === '' && $defaultProdi) {
                $v['homebase'] = $defaultProdi->kode;
                $v['_present'][] = 'prodi_id';
            }
            $prodi = $this->matchProdi($v['homebase'], $prodiLookup);
            if ($v['homebase'] === '') {
                $e[] = 'Homebase wajib diisi';
            } elseif (! $prodi) {
                $e[] = "Homebase \"{$v['homebase']}\" tidak dikenal atau bukan prodi Anda";
            }
            if ($v['nama'] === '') {
                $e[] = 'Nama Dosen wajib diisi';
            }

            $limits = ['nama' => 255, 'kode_dosen' => 30, 'nidn_nidk' => 50, 'nuptk' => 50, 'email' => 255, 'no_hp' => 30, 'alamat' => 1000,
                'lulusan' => 255, 'pendidikan_magister_bidang' => 255, 'pendidikan_doktor_bidang' => 255, 'bidang_keahlian' => 255,
                'golongan' => 30, 'no_sertifikat_pendidik' => 100];
            foreach ($limits as $f => $max) {
                if (mb_strlen($v[$f]) > $max) {
                    $e[] = self::COLUMNS[$f]." maksimal {$max} karakter";
                }
            }
            if ($v['email'] !== '' && ! filter_var($v['email'], FILTER_VALIDATE_EMAIL)) {
                $e[] = "Email \"{$v['email']}\" tidak valid";
            }

            $jk = $this->normalizeGender($v['jenis_kelamin']);
            if ($v['jenis_kelamin'] !== '' && $jk === null) {
                $e[] = 'Jenis Kelamin harus L atau P';
            }

            $pendidikan = $this->normalizePendidikan($v['pendidikan_terakhir'] !== '' ? $v['pendidikan_terakhir'] : $v['lulusan']);
            if ($v['pendidikan_terakhir'] !== '' && $pendidikan === null) {
                $e[] = 'Pendidikan Terakhir harus salah satu dari '.implode('/', Dosen::PENDIDIKAN);
            }

            $jabatanKey = null;
            if ($v['jabatan_akademik'] !== '') {
                $jabatanKey = $jabatanByLabel[$this->norm($v['jabatan_akademik'])] ?? null;
                if (! $jabatanKey) {
                    $e[] = 'Jabatan Akademik harus salah satu dari: '.implode(', ', Dosen::JABATAN);
                }
            }

            // Status SIAKAD bisa beragam (Aktif, Tidak Aktif, Cuti, Pensiun, Keluar, ...).
            $isActive = ! preg_match('/tidak|non|keluar|pensiun|berhenti|meninggal|resign|mutasi|^0$|^t$|^n$/i', trim($v['aktif']));
            if ($pendidikan !== null && ! in_array('pendidikan_terakhir', $v['_present'], true)) {
                $v['_present'][] = 'pendidikan_terakhir'; // diturunkan dari kolom Lulusan
            }

            $key = $v['nidn_nidk'] !== '' ? 'nidn:'.$v['nidn_nidk']
                : ($v['kode_dosen'] !== '' ? 'kode:'.strtolower($v['kode_dosen'])
                : 'nama:'.($prodi?->id).'|'.mb_strtolower($v['nama']));
            if (isset($seen[$key])) {
                $e[] = "Dosen ganda dengan baris {$seen[$key]}";
            }

            if ($v['nidn_nidk'] !== '' && ! $e) {
                $existing = Dosen::where('nidn_nidk', $v['nidn_nidk'])->first();
                if ($existing && ! in_array($existing->prodi_id, $allowedProdiIds, true)) {
                    $e[] = "NIDN/NIDK {$v['nidn_nidk']} sudah terdaftar di prodi lain";
                }
            }

            if ($e) {
                $errors[] = "Baris {$line}: ".implode('; ', $e);

                continue;
            }

            $seen[$key] = $line;
            $rows[] = [
                'prodi_id' => $prodi->id,
                'kode_dosen' => $v['kode_dosen'] ?: null,
                'nama' => $v['nama'],
                'jenis_kelamin' => $jk,
                'nidn_nidk' => $v['nidn_nidk'] ?: null,
                'nuptk' => $v['nuptk'] ?: null,
                'email' => $v['email'] ?: null,
                'no_hp' => $v['no_hp'] ?: null,
                'alamat' => $v['alamat'] ?: null,
                'pendidikan_terakhir' => $pendidikan,
                'lulusan' => $v['lulusan'] ?: null,
                'pendidikan_magister_bidang' => $v['pendidikan_magister_bidang'] ?: null,
                'pendidikan_doktor_bidang' => $v['pendidikan_doktor_bidang'] ?: null,
                'bidang_keahlian' => $v['bidang_keahlian'] ?: null,
                'golongan' => $v['golongan'] ?: null,
                'jabatan_akademik' => $jabatanKey,
                'no_sertifikat_pendidik' => $v['no_sertifikat_pendidik'] ?: null,
                'is_active' => $isActive,
                '_present' => $v['_present'],
            ];
        }

        if ($errors) {
            return back()
                ->with('error', 'Import dibatalkan, tidak ada data yang disimpan. Perbaiki kesalahan berikut lalu unggah ulang.')
                ->with('import_errors', array_slice($errors, 0, 100));
        }
        if (! $rows) {
            return back()->with('error', 'File tidak berisi data dosen.');
        }

        $created = 0;
        $updated = 0;
        DB::transaction(function () use ($rows, &$created, &$updated) {
            foreach ($rows as $row) {
                $present = $row['_present'];
                unset($row['_present']);

                $dosen = null;
                if ($row['nidn_nidk']) {
                    $dosen = Dosen::where('nidn_nidk', $row['nidn_nidk'])->first();
                }
                if (! $dosen && $row['kode_dosen']) {
                    $dosen = Dosen::where('kode_dosen', $row['kode_dosen'])->where('prodi_id', $row['prodi_id'])->first();
                }
                if (! $dosen) {
                    $dosen = Dosen::where('prodi_id', $row['prodi_id'])->where('nama', $row['nama'])->first();
                }

                if ($dosen) {
                    // Kolom yang tidak ada di file tidak menimpa data lama.
                    $dosen->update(array_intersect_key($row, array_flip($present)));
                    $updated++;
                } else {
                    Dosen::create($row);
                    $created++;
                }
            }
        });

        return redirect()->route('admin.dosen.index')
            ->with('success', "Import selesai: {$created} dosen ditambahkan, {$updated} dosen diperbarui.");
    }

    // ---- pembacaan file -------------------------------------------------

    private function norm(?string $s): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim((string) $s)));
    }

    /** @return array{0:int,1:?array<int,string>} index baris judul & peta kolom->field */
    private function detectHeader(array $data, bool $homebaseOptional = false): array
    {
        $lookup = [];
        foreach (self::ALIASES as $field => $aliases) {
            foreach ($aliases as $a) {
                $lookup[$a] ??= $field;
            }
        }
        // "Alamat & Email" -> "alamatemail", yang juga alias kolom email biasa. Bedakan lewat
        // judul aslinya: bila ada "&"/"/"/"dan", anggap kolom gabungan.
        foreach (array_slice($data, 0, 20, true) as $i => $row) {
            $map = [];
            foreach ($row as $col => $title) {
                $raw = strtolower(trim((string) $title));
                $n = $this->norm($raw);
                if ($n === '') {
                    continue;
                }
                $isCombined = (bool) preg_match('/[&\/]|\bdan\b/', $raw);
                $field = match (true) {
                    $isCombined && $n === 'alamatemail' => '_alamat_email',
                    $isCombined && $n === 'namakontak' => '_nama_kontak',
                    $isCombined && in_array($n, ['kodenidn', 'kodedosennidn'], true) => '_kode_nidn',
                    $isCombined && in_array($n, ['golonganjabatan', 'goljabatan', 'pangkatjabatan', 'golonganjabatanakademik'], true) => '_golongan_jabatan',
                    default => $lookup[$n] ?? null,
                };
                if ($field && ! in_array($field, $map, true)) {
                    $map[$col] = $field;
                }
            }
            $hasNama = array_intersect(['nama', '_nama_kontak'], $map);
            if ($hasNama && ($homebaseOptional || in_array('homebase', $map, true))) {
                return [$i, $map];
            }
        }

        return [0, null];
    }

    /** Ambil nilai baris sesuai peta kolom, pecah kolom gabungan SIAKAD. */
    private function extractRow(array $raw, array $map): array
    {
        $v = array_fill_keys(array_merge(array_keys(self::COLUMNS)), '');
        $present = [];

        foreach ($map as $col => $field) {
            $val = trim(preg_replace('/[ \t]+/', ' ', (string) ($raw[$col] ?? '')));
            if (! str_starts_with($field, '_')) {
                $v[$field] = $val;
                $present[] = $field;

                continue;
            }

            $parts = array_values(array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $val))));
            switch ($field) {
                case '_kode_nidn':
                    $parts = count($parts) > 1 ? $parts : array_map('trim', explode('/', $val));
                    $v['kode_dosen'] = $parts[0] ?? '';
                    $v['nidn_nidk'] = $parts[1] ?? '';
                    array_push($present, 'kode_dosen', 'nidn_nidk');
                    break;
                case '_nama_kontak':
                    $v['nama'] = $parts[0] ?? '';
                    $v['no_hp'] = preg_match('/(\+?\d[\d\s\-]{7,})/', implode(' ', array_slice($parts, 1)), $m) ? preg_replace('/[\s\-]/', '', $m[1]) : '';
                    array_push($present, 'nama', 'no_hp');
                    break;
                case '_alamat_email':
                    $v['email'] = preg_match('/[^\s@]+@[^\s@]+\.[^\s@]+/', $val, $m) ? $m[0] : '';
                    $v['alamat'] = trim(str_replace($v['email'], '', implode(', ', $parts)), " ,\t");
                    array_push($present, 'alamat', 'email');
                    break;
                case '_golongan_jabatan':
                    $v['golongan'] = preg_match('/\b(I{1,3}|IV)\s*\/\s*[a-e]\b/i', $val, $m) ? strtoupper(preg_replace('/\s/', '', explode('/', $m[0])[0])).'/'.strtolower(trim(explode('/', $m[0])[1])) : '';
                    $v['jabatan_akademik'] = '';
                    foreach (Dosen::JABATAN as $label) {
                        if (stripos($val, $label) !== false && mb_strlen($label) > mb_strlen($v['jabatan_akademik'])) {
                            $v['jabatan_akademik'] = $label; // "Lektor Kepala" menang atas "Lektor"
                        }
                    }
                    array_push($present, 'golongan', 'jabatan_akademik');
                    break;
            }
        }

        // NIDN/NUPTK dari Excel bisa terbaca sebagai angka berformat "6.12E+9"/"612345678.0".
        foreach (['nidn_nidk', 'nuptk'] as $f) {
            if (preg_match('/^\d+\.0+$/', $v[$f])) {
                $v[$f] = explode('.', $v[$f])[0];
            }
        }

        $present = array_values(array_unique(array_map(fn ($f) => $f === 'aktif' ? 'is_active' : $f, $present)));
        // Field turunan yang ikut ditimpa bila sumbernya ada di file.
        if (in_array('homebase', $present, true)) {
            $present[] = 'prodi_id';
        }
        $v['_present'] = array_values(array_diff($present, ['homebase']));

        return $v;
    }

    private function normalizeGender(string $s): ?string
    {
        $n = $this->norm($s);

        return match (true) {
            $n === '' => null,
            in_array($n, ['l', 'lk', 'laki', 'lakilaki', 'pria', 'm', 'male'], true) => 'L',
            in_array($n, ['p', 'pr', 'perempuan', 'wanita', 'f', 'female', 'w'], true) => 'P',
            default => null,
        };
    }

    /** "S2", "s-2", "Magister", "S2 - Universitas X" -> "S2". */
    private function normalizePendidikan(string $s): ?string
    {
        $n = $this->norm($s);
        if ($n === '') {
            return null;
        }
        foreach (['sp2' => 'Sp-2', 'sp1' => 'Sp-1', 's3' => 'S3', 's2' => 'S2', 's1' => 'S1', 'profesi' => 'Profesi',
            'doktor' => 'S3', 'magister' => 'S2', 'sarjana' => 'S1'] as $needle => $val) {
            if (str_starts_with($n, $needle)) {
                return $val;
            }
        }

        return null;
    }

    private function allowedProdis()
    {
        $user = auth()->user();

        return Prodi::when(! $user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->where('kaprodi_id', $user->id))
            ->orderBy('kode')->get();
    }

    /**
     * Cocokkan homebase: persis (kode / nama / "jenjang nama"), lalu longgar —
     * nama prodi terkandung di teks homebase, misal "Teknik Informatika (S1)" atau
     * "Prodi S1 Teknik Informatika - Fakultas Teknik". Nama terpanjang yang menang.
     */
    private function matchProdi(string $homebase, array $lookup): ?Prodi
    {
        $n = $this->norm($homebase);
        if ($n === '') {
            return null;
        }
        if (isset($lookup[$n])) {
            return $lookup[$n];
        }

        $best = null;
        foreach ($this->allowedProdis() as $p) {
            $nama = $this->norm($p->nama);
            if ($nama !== '' && str_contains($n, $nama) && (! $best || mb_strlen($nama) > mb_strlen($this->norm($best->nama)))) {
                // Bila teks menyebut jenjang lain (mis. "S2 ..." vs prodi S1), jangan dicocokkan.
                if (preg_match('/\b(d3|d4|s1|s2|s3)\b/i', $homebase, $m) && strcasecmp($m[1], $p->jenjang) !== 0) {
                    continue;
                }
                $best = $p;
            }
        }

        return $best;
    }

    /** Baca file dengan pembaca yang sesuai isinya (xlsx/xls/ods/csv/HTML ber-ekstensi .xls). */
    private function loadSpreadsheet(string $path, string $ext): ?Spreadsheet
    {
        $candidates = [];
        try {
            $candidates[] = IOFactory::identify($path);
        } catch (\Throwable $e) {
            // lanjut ke tebakan berikut
        }
        $candidates = array_merge($candidates, match ($ext) {
            'csv', 'txt' => ['Csv'],
            'htm', 'html' => ['Html'],
            default => ['Xlsx', 'Xls', 'Ods', 'Html', 'Csv'],
        });

        foreach (array_unique($candidates) as $type) {
            try {
                $reader = IOFactory::createReader($type);
                if ($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Csv) {
                    $reader->setInputEncoding(\PhpOffice\PhpSpreadsheet\Reader\Csv::GUESS_ENCODING);
                }
                if (! $reader->canRead($path)) {
                    continue;
                }
                $reader->setReadDataOnly(true);
                $book = $reader->load($path);
                if ($book->getSheetCount() > 0) {
                    return $book;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return null;
    }

    private function firstNonEmptyRow(array $data): array
    {
        foreach (array_slice($data, 0, 20) as $row) {
            $vals = array_values(array_filter(array_map(fn ($v) => trim((string) $v), $row), fn ($v) => $v !== ''));
            if (count($vals) >= 2) {
                return $vals;
            }
        }

        return [];
    }

    /** Homebase bisa ditulis sebagai kode, nama, atau "jenjang nama" prodi. */
    private function prodiLookup(): array
    {
        $lookup = [];
        foreach ($this->allowedProdis() as $p) {
            foreach ([$p->kode, $p->nama, "{$p->jenjang} {$p->nama}", "{$p->nama} {$p->jenjang}", "{$p->jenjang} - {$p->nama}"] as $k) {
                $lookup[$this->norm($k)] ??= $p;
            }
        }

        return $lookup;
    }

    // ---- penulisan file -------------------------------------------------

    private function buildSpreadsheet(array $rows): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Dosen');

        $headers = array_values(self::COLUMNS);
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->fromArray($headers, null, 'A1');
        foreach ($rows as $r => $row) {
            foreach ($row as $c => $value) {
                // Semua sebagai teks agar NIDN/HP yang diawali 0 tidak berubah.
                $sheet->setCellValueExplicit([$c + 1, $r + 2], (string) $value, DataType::TYPE_STRING);
            }
        }

        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A1:{$lastCol}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
        $sheet->freezePane('A2');
        $widths = [12, 14, 18, 34, 8, 16, 28, 36, 12, 10, 30, 24, 24, 28, 10, 18, 22, 8];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }
        $sheet->getStyle("A2:{$lastCol}1000")->getNumberFormat()->setFormatCode('@');

        $col = fn (string $field) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(array_search($field, array_keys(self::COLUMNS), true) + 1);
        $this->addListValidation($sheet, $col('jenis_kelamin').'2:'.$col('jenis_kelamin').'1000', array_keys(Dosen::JENIS_KELAMIN));
        $this->addListValidation($sheet, $col('pendidikan_terakhir').'2:'.$col('pendidikan_terakhir').'1000', Dosen::PENDIDIKAN);
        $this->addListValidation($sheet, $col('jabatan_akademik').'2:'.$col('jabatan_akademik').'1000', array_values(Dosen::JABATAN));
        $this->addListValidation($sheet, $col('aktif').'2:'.$col('aktif').'1000', ['Ya', 'Tidak']);

        return $spreadsheet;
    }

    private function addListValidation(Worksheet $sheet, string $range, array $options): void
    {
        $v = new DataValidation();
        $v->setType(DataValidation::TYPE_LIST)
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setAllowBlank(true)
            ->setShowDropDown(true)
            ->setShowErrorMessage(true)
            ->setErrorTitle('Isian tidak valid')
            ->setError('Pilih dari daftar: '.implode(', ', $options))
            ->setFormula1('"'.implode(',', $options).'"');
        $sheet->setDataValidation($range, $v);
    }

    private function download(Spreadsheet $spreadsheet, string $filename)
    {
        return response()->streamDownload(function () use ($spreadsheet) {
            IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
