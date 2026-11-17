<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akreditasi;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export / import Data Akreditasi lewat Excel.
 *
 * Kolom dibaca berdasarkan judul (urutan bebas). Pencocokan data lama:
 * Program Studi + Nomor SK -> diperbarui; selain itu ditambahkan.
 * File SK tidak bisa diimpor (unggah lewat form); kolom "Link SK" bisa.
 * Seluruh file divalidasi dulu; bila ada baris salah, tidak ada yang disimpan.
 */
class AkreditasiExcelController extends Controller implements HasMiddleware
{
    private const COLUMNS = [
        'prodi' => 'Kode Prodi',
        'prodi_nama' => 'Nama Program Studi',
        'lembaga' => 'Lembaga',
        'peringkat' => 'Peringkat',
        'nomor_sk' => 'Nomor SK',
        'tanggal_sk' => 'Tanggal SK',
        'tanggal_kadaluarsa' => 'Tanggal Kadaluarsa',
        'status' => 'Status',
        'link_sk' => 'Link SK',
        'file_sk' => 'File SK (unduh)',
        'catatan' => 'Catatan',
    ];

    /** Judul kolom yang dikenali saat import (dinormalisasi: huruf kecil a-z0-9). */
    private const ALIASES = [
        'prodi' => ['kodeprodi', 'prodi', 'programstudi', 'homebase', 'kode'],
        'prodi_nama' => ['namaprogramstudi', 'namaprodi'],
        'lembaga' => ['lembaga', 'lembagaakreditasi', 'lam', 'akreditor'],
        'peringkat' => ['peringkat', 'nilai', 'predikat', 'akreditasi', 'peringkatakreditasi'],
        'nomor_sk' => ['nomorsk', 'nosk', 'sk', 'nomorsuratkeputusan'],
        'tanggal_sk' => ['tanggalsk', 'tglsk', 'tanggalpenetapan', 'tglpenetapan', 'berlakumulai', 'mulaiberlaku'],
        'tanggal_kadaluarsa' => ['tanggalkadaluarsa', 'tglkadaluarsa', 'kadaluarsa', 'berlakusampai', 'masaberlaku', 'tanggalkedaluwarsa', 'kedaluwarsa', 'tanggalberakhir'],
        'status' => ['status'],
        'link_sk' => ['linksk', 'link', 'urlsk', 'tautansk'],
        'catatan' => ['catatan', 'keterangan', 'ket'],
    ];

    private const STATUS = [
        'aktif' => 'Aktif',
        'proses_perpanjangan' => 'Proses Perpanjangan',
        'kadaluarsa' => 'Kadaluarsa',
    ];

    private const LEMBAGA_CONTOH = ['BAN-PT', 'LAM-PTKes', 'LAM-PTIK', 'LAM-Teknik', 'LAM INFOKOM', 'LAMDIK', 'LAMEMBA', 'Internasional'];

    private const PERINGKAT_CONTOH = ['Unggul', 'Baik Sekali', 'Baik', 'A', 'B', 'C'];

    public static function middleware(): array
    {
        return [
            new Middleware('permission:akreditasi.view', only: ['export']),
            new Middleware('permission:akreditasi.create', only: ['template', 'import']),
        ];
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $rows = Akreditasi::with('prodi')
            ->when(! $user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->ownedByKaprodi($user))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('lembaga'), fn ($q) => $q->where('lembaga', $request->lembaga))
            ->orderBy('prodi_id')->orderByDesc('tanggal_sk')->get()
            ->map(fn (Akreditasi $a) => $this->orderedRow([
                'prodi' => $a->prodi?->kode,
                'prodi_nama' => trim(($a->prodi?->jenjang ?? '').' '.($a->prodi?->nama ?? '')),
                'lembaga' => $a->lembaga,
                'peringkat' => $a->peringkat,
                'nomor_sk' => $a->nomor_sk,
                'tanggal_sk' => $a->tanggal_sk?->format('Y-m-d'),
                'tanggal_kadaluarsa' => $a->tanggal_kadaluarsa?->format('Y-m-d'),
                'status' => self::STATUS[$a->status] ?? $a->status,
                'link_sk' => $a->link_sk,
                'file_sk' => $a->file_sk ? url(\Illuminate\Support\Facades\Storage::url($a->file_sk)) : '',
                'catatan' => $a->catatan,
            ]))->all();

        return $this->download($this->buildSpreadsheet($rows), 'data-akreditasi-'.now()->format('Ymd-His').'.xlsx');
    }

    public function template()
    {
        $prodi = $this->allowedProdis()->first();
        $spreadsheet = $this->buildSpreadsheet([$this->orderedRow([
            'prodi' => $prodi?->kode ?? 'TI',
            'prodi_nama' => $prodi ? "{$prodi->jenjang} {$prodi->nama}" : 'S1 Teknik Informatika',
            'lembaga' => 'LAM INFOKOM',
            'peringkat' => 'Baik Sekali',
            'nomor_sk' => '123/SK/LAM-INFOKOM/Ak/S/XI/2025',
            'tanggal_sk' => '2025-11-10',
            'tanggal_kadaluarsa' => '2030-11-10',
            'status' => '',
            'link_sk' => 'https://drive.google.com/...',
            'catatan' => '',
        ])]);

        $info = $spreadsheet->createSheet();
        $info->setTitle('Petunjuk');
        $info->fromArray([
            ['PETUNJUK PENGISIAN'],
            [''],
            ['Kode Prodi', 'WAJIB. Kode program studi (lihat daftar di bawah). Boleh juga nama prodi, mis. "S1 Teknik Informatika".'],
            ['Nama Program Studi', 'Hanya keterangan, tidak dibaca saat import.'],
            ['Lembaga', 'WAJIB. Mis. '.implode(', ', self::LEMBAGA_CONTOH).'.'],
            ['Peringkat', 'WAJIB. Mis. '.implode(', ', self::PERINGKAT_CONTOH).'.'],
            ['Nomor SK', 'WAJIB. Prodi + Nomor SK yang sudah ada akan DIPERBARUI, selain itu DITAMBAHKAN.'],
            ['Tanggal SK', 'WAJIB. Format tanggal Excel, atau teks YYYY-MM-DD / DD-MM-YYYY.'],
            ['Tanggal Kadaluarsa', 'WAJIB, harus setelah Tanggal SK.'],
            ['Status', 'Aktif / Proses Perpanjangan / Kadaluarsa. Dikosongkan = ditentukan dari Tanggal Kadaluarsa.'],
            ['Link SK', 'Opsional. Tautan Google Drive / situs LAM. File SK (PDF/gambar) diunggah lewat form Edit.'],
            ['File SK (unduh)', 'Hanya keterangan pada hasil export, tidak dibaca saat import.'],
            [''],
            ['Kode Prodi', 'Nama Program Studi'],
        ]);
        $info->fromArray($this->allowedProdis()->map(fn ($p) => [$p->kode, "{$p->jenjang} {$p->nama}"])->all(), null, 'A15');
        $info->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $info->getStyle('A3:A12')->getFont()->setBold(true);
        $info->getStyle('A14:B14')->getFont()->setBold(true);
        $info->getColumnDimension('A')->setWidth(22);
        $info->getColumnDimension('B')->setWidth(110);
        $info->getStyle('A:A')->getNumberFormat()->setFormatCode('@');
        $spreadsheet->setActiveSheetIndex(0);

        return $this->download($spreadsheet, 'template-import-akreditasi.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['xlsx', 'xls', 'csv', 'ods'], true)) {
            return back()->with('error', 'Format file harus Excel (.xlsx/.xls), .csv, atau .ods.');
        }

        try {
            $sheet = IOFactory::load($file->getRealPath())->getSheet(0);
        } catch (\Throwable $e) {
            return back()->with('error', 'File tidak dapat dibaca. Pastikan file Excel tidak rusak.');
        }

        $data = $sheet->toArray(null, true, false, false);
        [$headerRow, $map] = $this->detectHeader($data);
        if ($map === null) {
            return back()->with('error', 'Kolom wajib (Kode Prodi, Lembaga, Peringkat, Nomor SK, Tanggal SK, Tanggal Kadaluarsa) tidak ditemukan. Gunakan file dari tombol "Template Import".');
        }

        $prodiLookup = $this->prodiLookup();
        $errors = [];
        $rows = [];
        $seen = [];

        foreach (array_slice($data, $headerRow + 1, null, true) as $idx => $raw) {
            $line = $idx + 1;
            $v = array_fill_keys(array_keys(self::ALIASES), '');
            foreach ($map as $col => $field) {
                $v[$field] = $this->cleanCell($raw[$col] ?? '');
            }
            if (implode('', $v) === '') {
                continue;
            }

            $e = [];
            $prodi = $prodiLookup[$this->norm($v['prodi'])] ?? null;
            if ($v['prodi'] === '') {
                $e[] = 'Kode Prodi wajib diisi';
            } elseif (! $prodi) {
                $e[] = "Prodi \"{$v['prodi']}\" tidak dikenal atau bukan prodi Anda";
            }
            foreach (['lembaga' => 'Lembaga', 'peringkat' => 'Peringkat', 'nomor_sk' => 'Nomor SK'] as $f => $label) {
                if ($v[$f] === '') {
                    $e[] = "{$label} wajib diisi";
                }
            }
            foreach (['lembaga' => 100, 'peringkat' => 50, 'nomor_sk' => 255] as $f => $max) {
                if (mb_strlen($v[$f]) > $max) {
                    $e[] = self::COLUMNS[$f]." maksimal {$max} karakter";
                }
            }

            $tglSk = $this->parseDate($v['tanggal_sk']);
            $tglExp = $this->parseDate($v['tanggal_kadaluarsa']);
            if (! $tglSk) {
                $e[] = $v['tanggal_sk'] === '' ? 'Tanggal SK wajib diisi' : "Tanggal SK \"{$v['tanggal_sk']}\" tidak valid";
            }
            if (! $tglExp) {
                $e[] = $v['tanggal_kadaluarsa'] === '' ? 'Tanggal Kadaluarsa wajib diisi' : "Tanggal Kadaluarsa \"{$v['tanggal_kadaluarsa']}\" tidak valid";
            }
            if ($tglSk && $tglExp && $tglExp <= $tglSk) {
                $e[] = 'Tanggal Kadaluarsa harus setelah Tanggal SK';
            }

            $status = $this->normalizeStatus($v['status'], $tglExp);
            if ($v['status'] !== '' && $status === null) {
                $e[] = 'Status harus Aktif / Proses Perpanjangan / Kadaluarsa';
            }

            if ($v['link_sk'] !== '' && ! preg_match('#^https?://\S+$#i', $v['link_sk'])) {
                $e[] = 'Link SK harus diawali http:// atau https://';
            }

            $key = ($prodi?->id).'|'.mb_strtolower($v['nomor_sk']);
            if (isset($seen[$key])) {
                $e[] = "Data ganda dengan baris {$seen[$key]}";
            }

            if ($e) {
                $errors[] = "Baris {$line}: ".implode('; ', $e);

                continue;
            }

            $seen[$key] = $line;
            $rows[] = [
                'prodi_id' => $prodi->id,
                'lembaga' => $v['lembaga'],
                'peringkat' => $v['peringkat'],
                'nomor_sk' => $v['nomor_sk'],
                'tanggal_sk' => $tglSk,
                'tanggal_kadaluarsa' => $tglExp,
                'status' => $status,
                'link_sk' => $v['link_sk'] ?: null,
                'catatan' => $v['catatan'] ?: null,
                '_has' => ['link_sk' => in_array('link_sk', $map, true), 'catatan' => in_array('catatan', $map, true)],
            ];
        }

        if ($errors) {
            return back()
                ->with('error', 'Import dibatalkan, tidak ada data yang disimpan. Perbaiki kesalahan berikut lalu unggah ulang.')
                ->with('import_errors', array_slice($errors, 0, 100));
        }
        if (! $rows) {
            return back()->with('error', 'File tidak berisi data akreditasi.');
        }

        $created = 0;
        $updated = 0;
        DB::transaction(function () use ($rows, &$created, &$updated) {
            foreach ($rows as $row) {
                $has = $row['_has'];
                unset($row['_has']);

                $existing = Akreditasi::where('prodi_id', $row['prodi_id'])->where('nomor_sk', $row['nomor_sk'])->first();
                if ($existing) {
                    // Link/Catatan kosong di file tidak menghapus isian lama.
                    foreach (['link_sk', 'catatan'] as $f) {
                        if (! $has[$f] || $row[$f] === null) {
                            unset($row[$f]);
                        }
                    }
                    $existing->update($row);
                    $updated++;
                } else {
                    Akreditasi::create($row);
                    $created++;
                }
            }
        });

        return redirect()->route('admin.akreditasi.index')
            ->with('success', "Import selesai: {$created} akreditasi ditambahkan, {$updated} diperbarui.");
    }

    // ---------------------------------------------------------------------

    private function norm(?string $s): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim((string) $s)));
    }

    private function cleanCell(mixed $value): string
    {
        $s = trim(preg_replace('/\s+/u', ' ', (string) $value));
        $s = trim(preg_replace("/^'+/", '', $s));

        return preg_match('/^(-+|null|n\/a)$/i', $s) ? '' : $s;
    }

    /** @return array{0:int,1:?array<int,string>} */
    private function detectHeader(array $data): array
    {
        $lookup = [];
        foreach (self::ALIASES as $field => $aliases) {
            foreach ($aliases as $a) {
                $lookup[$a] ??= $field;
            }
        }
        $required = ['prodi', 'lembaga', 'peringkat', 'nomor_sk', 'tanggal_sk', 'tanggal_kadaluarsa'];

        foreach (array_slice($data, 0, 20, true) as $i => $row) {
            $map = [];
            foreach ($row as $col => $title) {
                $field = $lookup[$this->norm((string) $title)] ?? null;
                if ($field && ! in_array($field, $map, true)) {
                    $map[$col] = $field;
                }
            }
            if (! array_diff($required, $map)) {
                return [$i, $map];
            }
        }

        return [0, null];
    }

    /** Serial Excel, YYYY-MM-DD, atau DD-MM-YYYY / DD/MM/YYYY. */
    private function parseDate(string $s): ?string
    {
        if ($s === '') {
            return null;
        }
        try {
            if (is_numeric($s) && (float) $s > 1000 && (float) $s < 80000) {
                return ExcelDate::excelToDateTimeObject((float) $s)->format('Y-m-d');
            }
            if (preg_match('/^(\d{4})[\/.\-](\d{1,2})[\/.\-](\d{1,2})/', $s, $m)) {
                [$y, $mo, $d] = [(int) $m[1], (int) $m[2], (int) $m[3]];
            } elseif (preg_match('/^(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{4})/', $s, $m)) {
                [$d, $mo, $y] = [(int) $m[1], (int) $m[2], (int) $m[3]];
            } else {
                $ts = strtotime($s);

                return $ts ? date('Y-m-d', $ts) : null;
            }

            return checkdate($mo, $d, $y) ? sprintf('%04d-%02d-%02d', $y, $mo, $d) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Status kosong -> dari tanggal kadaluarsa. */
    private function normalizeStatus(string $s, ?string $tglExp): ?string
    {
        $n = $this->norm($s);
        if ($n === '') {
            return $tglExp && $tglExp < now()->toDateString() ? 'kadaluarsa' : 'aktif';
        }

        return match (true) {
            str_contains($n, 'proses') || str_contains($n, 'perpanjang') => 'proses_perpanjangan',
            str_contains($n, 'kadaluarsa') || str_contains($n, 'kedaluwarsa') || str_contains($n, 'expired') || $n === 'tidakaktif' => 'kadaluarsa',
            $n === 'aktif' || $n === 'berlaku' || $n === 'active' => 'aktif',
            default => null,
        };
    }

    private function allowedProdis()
    {
        $user = auth()->user();

        return Prodi::when(! $user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->where('kaprodi_id', $user->id))
            ->orderBy('kode')->get();
    }

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

    private function orderedRow(array $values): array
    {
        return array_map(fn ($f) => $values[$f] ?? '', array_keys(self::COLUMNS));
    }

    private function buildSpreadsheet(array $rows): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Akreditasi');

        $headers = array_values(self::COLUMNS);
        $last = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->fromArray($headers, null, 'A1');
        foreach ($rows as $r => $row) {
            foreach ($row as $c => $value) {
                $sheet->setCellValueExplicit([$c + 1, $r + 2], (string) $value, DataType::TYPE_STRING);
            }
        }

        $sheet->getStyle("A1:{$last}1")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A1:{$last}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
        $sheet->freezePane('A2');
        foreach ([10, 28, 16, 14, 36, 14, 18, 20, 36, 36, 30] as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }
        $sheet->getStyle("A2:{$last}1000")->getNumberFormat()->setFormatCode('@');

        $col = fn (string $f) => Coordinate::stringFromColumnIndex(array_search($f, array_keys(self::COLUMNS), true) + 1);
        $this->addListValidation($sheet, $col('status').'2:'.$col('status').'1000', array_values(self::STATUS), false);
        $this->addListValidation($sheet, $col('lembaga').'2:'.$col('lembaga').'1000', self::LEMBAGA_CONTOH, true);
        $this->addListValidation($sheet, $col('peringkat').'2:'.$col('peringkat').'1000', self::PERINGKAT_CONTOH, true);

        return $spreadsheet;
    }

    /** $saranSaja: dropdown sebagai saran, isian lain tetap boleh (Lembaga/Peringkat bisa beragam). */
    private function addListValidation(Worksheet $sheet, string $range, array $options, bool $saranSaja): void
    {
        $v = new DataValidation();
        $v->setType(DataValidation::TYPE_LIST)
            ->setErrorStyle($saranSaja ? DataValidation::STYLE_INFORMATION : DataValidation::STYLE_STOP)
            ->setAllowBlank(true)
            ->setShowDropDown(true)
            ->setShowErrorMessage(! $saranSaja)
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
