<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\User;
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
 * Export / import data Program Studi lewat Excel.
 *
 * Kolom: Kode | Jenjang | Nama Program Studi | Fakultas | Email Kaprodi | Aktif
 * Import bersifat "upsert" berdasarkan Kode: kode baru ditambahkan, kode yang
 * sudah ada diperbarui. Seluruh file divalidasi dulu; bila ada satu baris
 * salah, tidak ada data yang disimpan.
 */
class ProdiExcelController extends Controller implements HasMiddleware
{
    private const HEADERS = ['Kode', 'Jenjang', 'Nama Program Studi', 'Fakultas', 'Email Kaprodi', 'Aktif'];

    private const JENJANG = ['D3', 'D4', 'S1', 'S2', 'S3'];

    public static function middleware(): array
    {
        return [
            new Middleware('permission:prodi.view', only: ['export']),
            new Middleware('permission:prodi.create', only: ['template', 'import']),
        ];
    }

    public function export()
    {
        $rows = Prodi::with('kaprodi')->orderBy('jenjang')->orderBy('nama')->get()
            ->map(fn (Prodi $p) => [
                $p->kode,
                $p->jenjang,
                $p->nama,
                $p->fakultas,
                $p->kaprodi?->email,
                $p->is_active ? 'Ya' : 'Tidak',
            ])->all();

        $spreadsheet = $this->buildSpreadsheet($rows);

        return $this->download($spreadsheet, 'data-prodi-'.now()->format('Ymd-His').'.xlsx');
    }

    public function template()
    {
        $spreadsheet = $this->buildSpreadsheet([
            ['TI', 'S1', 'Teknik Informatika', 'Fakultas Teknik', '', 'Ya'],
        ]);

        $info = $spreadsheet->createSheet();
        $info->setTitle('Petunjuk');
        $lines = [
            ['PETUNJUK PENGISIAN'],
            [''],
            ['Kode', 'Wajib, maksimal 20 karakter, unik. Kode yang sudah ada akan DIPERBARUI, kode baru akan DITAMBAHKAN.'],
            ['Jenjang', 'Wajib. Salah satu dari: '.implode(', ', self::JENJANG)],
            ['Nama Program Studi', 'Wajib.'],
            ['Fakultas', 'Opsional.'],
            ['Email Kaprodi', 'Opsional. Email akun pengguna yang SUDAH terdaftar di menu Pengguna. Dikosongkan = kaprodi tidak diubah.'],
            ['Aktif', 'Ya / Tidak. Dikosongkan = Ya.'],
            [''],
            ['Hapus baris contoh sebelum mengunggah. Jangan mengubah urutan atau judul kolom di baris pertama.'],
        ];
        $info->fromArray($lines);
        $info->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $info->getStyle('A3:A8')->getFont()->setBold(true);
        $info->getColumnDimension('A')->setWidth(22);
        $info->getColumnDimension('B')->setWidth(110);

        $spreadsheet->setActiveSheetIndex(0);

        return $this->download($spreadsheet, 'template-import-prodi.xlsx');
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

        $usersByEmail = User::pluck('id', 'email')->mapWithKeys(fn ($id, $email) => [strtolower($email) => $id]);
        $errors = [];
        $rows = [];
        $seenKode = [];

        foreach (array_slice($data, 1) as $i => $row) {
            $line = $i + 2; // nomor baris di Excel
            [$kode, $jenjang, $nama, $fakultas, $email, $aktif] = array_map(
                fn ($v) => trim((string) $v),
                array_pad(array_slice($row, 0, 6), 6, '')
            );

            if ($kode === '' && $jenjang === '' && $nama === '' && $fakultas === '' && $email === '' && $aktif === '') {
                continue; // baris kosong
            }

            $jenjang = strtoupper($jenjang);
            $rowErrors = [];

            if ($kode === '') {
                $rowErrors[] = 'Kode wajib diisi';
            } elseif (mb_strlen($kode) > 20) {
                $rowErrors[] = 'Kode maksimal 20 karakter';
            } elseif (isset($seenKode[strtolower($kode)])) {
                $rowErrors[] = "Kode \"{$kode}\" ganda (sudah ada di baris {$seenKode[strtolower($kode)]})";
            }
            if (! in_array($jenjang, self::JENJANG, true)) {
                $rowErrors[] = 'Jenjang harus salah satu dari '.implode('/', self::JENJANG);
            }
            if ($nama === '') {
                $rowErrors[] = 'Nama Program Studi wajib diisi';
            } elseif (mb_strlen($nama) > 255) {
                $rowErrors[] = 'Nama maksimal 255 karakter';
            }
            if (mb_strlen($fakultas) > 255) {
                $rowErrors[] = 'Fakultas maksimal 255 karakter';
            }
            $kaprodiId = null;
            if ($email !== '') {
                $kaprodiId = $usersByEmail[strtolower($email)] ?? null;
                if (! $kaprodiId) {
                    $rowErrors[] = "Email kaprodi \"{$email}\" belum terdaftar di menu Pengguna";
                }
            }
            $aktifNorm = strtolower($aktif);
            if (! in_array($aktifNorm, ['', 'ya', 'tidak', '1', '0', 'aktif', 'nonaktif'], true)) {
                $rowErrors[] = 'Kolom Aktif harus Ya atau Tidak';
            }

            if ($rowErrors) {
                $errors[] = "Baris {$line}: ".implode('; ', $rowErrors);

                continue;
            }

            $seenKode[strtolower($kode)] = $line;
            $rows[] = [
                'kode' => $kode,
                'jenjang' => $jenjang,
                'nama' => $nama,
                'fakultas' => $fakultas !== '' ? $fakultas : null,
                'kaprodi_id' => $kaprodiId,
                'is_active' => ! in_array($aktifNorm, ['tidak', '0', 'nonaktif'], true),
            ];
        }

        if ($errors) {
            return back()
                ->with('error', 'Import dibatalkan, tidak ada data yang disimpan. Perbaiki kesalahan berikut lalu unggah ulang.')
                ->with('import_errors', $errors);
        }

        if (! $rows) {
            return back()->with('error', 'File tidak berisi data prodi.');
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($rows, &$created, &$updated) {
            $kaprodiRole = Role::where('slug', 'kaprodi')->first();
            $touchedUsers = [];

            foreach ($rows as $row) {
                $prodi = Prodi::where('kode', $row['kode'])->first();
                $oldKaprodiId = $prodi?->kaprodi_id;

                // Email kaprodi kosong = kaprodi tidak diubah.
                if ($row['kaprodi_id'] === null) {
                    unset($row['kaprodi_id']);
                }

                if ($prodi) {
                    $prodi->update($row);
                    $updated++;
                } else {
                    $prodi = Prodi::create($row);
                    $created++;
                }

                array_push($touchedUsers, $oldKaprodiId, $prodi->kaprodi_id);
            }

            // Selaraskan role "Kepala Program Studi" dengan kolom kaprodi_id.
            if ($kaprodiRole) {
                foreach (array_unique(array_filter($touchedUsers)) as $userId) {
                    $user = User::find($userId);
                    if (! $user) {
                        continue;
                    }
                    Prodi::where('kaprodi_id', $userId)->exists()
                        ? $user->assignRole($kaprodiRole)
                        : $user->removeRole($kaprodiRole);
                }
            }
        });

        return redirect()->route('admin.prodi.index')
            ->with('success', "Import selesai: {$created} prodi ditambahkan, {$updated} prodi diperbarui.");
    }

    // ---------------------------------------------------------------------

    private function buildSpreadsheet(array $rows): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Prodi');
        $sheet->fromArray(self::HEADERS, null, 'A1');
        if ($rows) {
            $sheet->fromArray($rows, null, 'A2', true);
        }

        $sheet->getStyle('A1:F1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
        $sheet->freezePane('A2');
        foreach (['A' => 12, 'B' => 10, 'C' => 40, 'D' => 30, 'E' => 32, 'F' => 10] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        // Kode dibaca sebagai teks agar "01" tidak berubah jadi 1.
        $sheet->getStyle('A:A')->getNumberFormat()->setFormatCode('@');

        $this->addListValidation($sheet, 'B2:B500', self::JENJANG);
        $this->addListValidation($sheet, 'F2:F500', ['Ya', 'Tidak']);

        return $spreadsheet;
    }

    private function addListValidation(Worksheet $sheet, string $range, array $options): void
    {
        $validation = new DataValidation();
        $validation->setType(DataValidation::TYPE_LIST)
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setAllowBlank(true)
            ->setShowDropDown(true)
            ->setShowErrorMessage(true)
            ->setErrorTitle('Isian tidak valid')
            ->setError('Pilih dari daftar: '.implode(', ', $options))
            ->setFormula1('"'.implode(',', $options).'"');
        $sheet->setDataValidation($range, $validation);
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
