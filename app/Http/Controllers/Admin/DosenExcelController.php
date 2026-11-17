<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export / import Data Dosen lewat Excel.
 *
 * Pencocokan saat import: NIDN/NIDK bila diisi; bila kosong, Nama + Kode Prodi.
 * Data yang cocok diperbarui, sisanya ditambahkan. Seluruh file divalidasi
 * dulu; bila ada satu baris salah, tidak ada yang disimpan.
 */
class DosenExcelController extends Controller implements HasMiddleware
{
    private const HEADERS = [
        'Kode Prodi', 'Nama Dosen', 'NIDN/NIDK', 'NUPTK', 'Email',
        'Pendidikan S2 (Bidang)', 'Pendidikan S3 (Bidang)', 'Bidang Keahlian',
        'Jabatan Akademik', 'No. Sertifikat Pendidik', 'Aktif',
    ];

    private const LAST_COL = 'K';

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
                $d->prodi?->kode,
                $d->nama,
                $d->nidn_nidk,
                $d->nuptk,
                $d->email,
                $d->pendidikan_magister_bidang,
                $d->pendidikan_doktor_bidang,
                $d->bidang_keahlian,
                Dosen::JABATAN[$d->jabatan_akademik] ?? null,
                $d->no_sertifikat_pendidik,
                $d->is_active ? 'Ya' : 'Tidak',
            ])->all();

        return $this->download($this->buildSpreadsheet($rows), 'data-dosen-'.now()->format('Ymd-His').'.xlsx');
    }

    public function template()
    {
        $kodeContoh = $this->allowedProdis()->first()?->kode ?? 'TI';
        $spreadsheet = $this->buildSpreadsheet([
            [$kodeContoh, 'Dr. Nama Dosen, M.Kom.', '0612345678', '', 'dosen@unukase.ac.id', 'Ilmu Komputer', '', 'Rekayasa Perangkat Lunak', 'Lektor', '', 'Ya'],
        ]);

        $info = $spreadsheet->createSheet();
        $info->setTitle('Petunjuk');
        $info->fromArray([
            ['PETUNJUK PENGISIAN'],
            [''],
            ['Kode Prodi', 'Wajib. Kode program studi seperti di menu Program Studi. Daftar kode yang boleh dipakai ada di bawah.'],
            ['Nama Dosen', 'Wajib, lengkap dengan gelar.'],
            ['NIDN/NIDK', 'Opsional tetapi disarankan. Bila diisi dan sudah terdaftar, data dosen tersebut DIPERBARUI.'],
            ['', 'Bila NIDN kosong, pencocokan memakai Nama Dosen + Kode Prodi.'],
            ['Jabatan Akademik', 'Opsional. Salah satu dari: '.implode(', ', Dosen::JABATAN)],
            ['Aktif', 'Ya / Tidak. Dikosongkan = Ya.'],
            ['Kolom lain', 'Opsional.'],
            [''],
            ['Hapus baris contoh sebelum mengunggah. Jangan mengubah urutan atau judul kolom di baris pertama.'],
            [''],
            ['Kode Prodi', 'Nama Program Studi'],
        ]);
        $info->fromArray($this->allowedProdis()->map(fn ($p) => [$p->kode, "{$p->jenjang} {$p->nama}"])->all(), null, 'A14');
        $info->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $info->getStyle('A3:A9')->getFont()->setBold(true);
        $info->getStyle('A13:B13')->getFont()->setBold(true);
        $info->getColumnDimension('A')->setWidth(22);
        $info->getColumnDimension('B')->setWidth(110);
        $info->getStyle('A:A')->getNumberFormat()->setFormatCode('@');
        $spreadsheet->setActiveSheetIndex(0);

        return $this->download($spreadsheet, 'template-import-dosen.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        try {
            $sheet = IOFactory::load($request->file('file')->getRealPath())->getSheet(0);
        } catch (\Throwable $e) {
            return back()->with('error', 'File tidak dapat dibaca. Pastikan file Excel tidak rusak.');
        }

        $data = $sheet->toArray(null, true, true, false);
        $header = array_map(fn ($v) => strtolower(trim((string) $v)), $data[0] ?? []);
        $expected = array_map('strtolower', self::HEADERS);
        if (array_slice($header, 0, count($expected)) !== $expected) {
            return back()->with('error', 'Judul kolom tidak sesuai. Gunakan file dari tombol "Template Import".');
        }

        $prodiByKode = $this->allowedProdis()->keyBy(fn ($p) => strtolower($p->kode));
        $allowedProdiIds = $prodiByKode->pluck('id')->all();
        $jabatanByLabel = collect(Dosen::JABATAN)->mapWithKeys(fn ($label, $key) => [strtolower($label) => $key, $key => $key]);

        $errors = [];
        $rows = [];
        $seen = [];

        foreach (array_slice($data, 1) as $i => $raw) {
            $line = $i + 2;
            $c = array_map(fn ($v) => trim((string) $v), array_pad(array_slice($raw, 0, 11), 11, ''));
            if (implode('', $c) === '') {
                continue;
            }
            [$kodeProdi, $nama, $nidn, $nuptk, $email, $s2, $s3, $keahlian, $jabatan, $serdos, $aktif] = $c;
            $e = [];

            $prodi = $prodiByKode[strtolower($kodeProdi)] ?? null;
            if ($kodeProdi === '') {
                $e[] = 'Kode Prodi wajib diisi';
            } elseif (! $prodi) {
                $e[] = "Kode Prodi \"{$kodeProdi}\" tidak dikenal atau bukan prodi Anda";
            }
            if ($nama === '') {
                $e[] = 'Nama Dosen wajib diisi';
            }
            foreach ([[$nama, 255, 'Nama'], [$nidn, 50, 'NIDN/NIDK'], [$nuptk, 50, 'NUPTK'], [$email, 255, 'Email'], [$s2, 255, 'Pendidikan S2'], [$s3, 255, 'Pendidikan S3'], [$keahlian, 255, 'Bidang Keahlian'], [$serdos, 100, 'No. Sertifikat']] as [$val, $max, $label]) {
                if (mb_strlen($val) > $max) {
                    $e[] = "{$label} maksimal {$max} karakter";
                }
            }
            if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $e[] = "Email \"{$email}\" tidak valid";
            }
            $jabatanKey = null;
            if ($jabatan !== '') {
                $jabatanKey = $jabatanByLabel[strtolower($jabatan)] ?? null;
                if (! $jabatanKey) {
                    $e[] = 'Jabatan Akademik harus salah satu dari: '.implode(', ', Dosen::JABATAN);
                }
            }
            $aktifNorm = strtolower($aktif);
            if (! in_array($aktifNorm, ['', 'ya', 'tidak', '1', '0', 'aktif', 'nonaktif'], true)) {
                $e[] = 'Kolom Aktif harus Ya atau Tidak';
            }

            $key = $nidn !== '' ? 'nidn:'.$nidn : 'nama:'.strtolower($kodeProdi.'|'.$nama);
            if (isset($seen[$key])) {
                $e[] = "Dosen ganda dengan baris {$seen[$key]}";
            }

            // Dosen dengan NIDN ini sudah ada di prodi lain yang bukan milik user.
            if ($nidn !== '' && ! $e) {
                $existing = Dosen::where('nidn_nidk', $nidn)->first();
                if ($existing && ! in_array($existing->prodi_id, $allowedProdiIds, true)) {
                    $e[] = "NIDN/NIDK {$nidn} sudah terdaftar di prodi lain";
                }
            }

            if ($e) {
                $errors[] = "Baris {$line}: ".implode('; ', $e);

                continue;
            }

            $seen[$key] = $line;
            $rows[] = [
                'prodi_id' => $prodi->id,
                'nama' => $nama,
                'nidn_nidk' => $nidn !== '' ? $nidn : null,
                'nuptk' => $nuptk ?: null,
                'email' => $email ?: null,
                'pendidikan_magister_bidang' => $s2 ?: null,
                'pendidikan_doktor_bidang' => $s3 ?: null,
                'bidang_keahlian' => $keahlian ?: null,
                'jabatan_akademik' => $jabatanKey,
                'no_sertifikat_pendidik' => $serdos ?: null,
                'is_active' => ! in_array($aktifNorm, ['tidak', '0', 'nonaktif'], true),
            ];
        }

        if ($errors) {
            return back()
                ->with('error', 'Import dibatalkan, tidak ada data yang disimpan. Perbaiki kesalahan berikut lalu unggah ulang.')
                ->with('import_errors', $errors);
        }
        if (! $rows) {
            return back()->with('error', 'File tidak berisi data dosen.');
        }

        $created = 0;
        $updated = 0;
        DB::transaction(function () use ($rows, &$created, &$updated) {
            foreach ($rows as $row) {
                $dosen = $row['nidn_nidk']
                    ? Dosen::where('nidn_nidk', $row['nidn_nidk'])->first()
                    : Dosen::where('prodi_id', $row['prodi_id'])->where('nama', $row['nama'])->first();

                if ($dosen) {
                    $dosen->update($row);
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

    // ---------------------------------------------------------------------

    private function allowedProdis()
    {
        $user = auth()->user();

        return Prodi::when(! $user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->where('kaprodi_id', $user->id))
            ->orderBy('kode')->get();
    }

    private function buildSpreadsheet(array $rows): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Dosen');

        // NIDN/NUPTK/kode disimpan sebagai teks agar angka 0 di depan tidak hilang.
        foreach (['A', 'C', 'D', 'J'] as $col) {
            $sheet->getStyle("{$col}:{$col}")->getNumberFormat()->setFormatCode('@');
        }
        $sheet->fromArray(self::HEADERS, null, 'A1');
        foreach ($rows as $r => $row) {
            foreach ($row as $c => $value) {
                $sheet->setCellValueExplicit([$c + 1, $r + 2], (string) $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
        }

        $range = 'A1:'.self::LAST_COL.'1';
        $sheet->getStyle($range)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
        $sheet->freezePane('A2');
        foreach (['A' => 12, 'B' => 34, 'C' => 14, 'D' => 18, 'E' => 28, 'F' => 24, 'G' => 24, 'H' => 28, 'I' => 18, 'J' => 22, 'K' => 8] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $this->addListValidation($sheet, 'I2:I1000', array_values(Dosen::JABATAN));
        $this->addListValidation($sheet, 'K2:K1000', ['Ya', 'Tidak']);

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
