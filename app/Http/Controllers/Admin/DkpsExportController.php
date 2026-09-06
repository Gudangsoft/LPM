<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DkpsSubmission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DkpsExportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:dkps.view'),
        ];
    }

    public function export(DkpsSubmission $dkp)
    {
        $dkp->load([
            'prodi', 'akreditasi', 'dosenTetap', 'tenagaKependidikan',
            'kerjasama', 'kualitasInput', 'prestasiMahasiswa', 'karyaInovatifMahasiswa', 'kepuasanMahasiswa',
            'bebanKerjaDtps.dosenTetap', 'rekognisiDtps.dosenTetap', 'pengembanganKompetensi',
            'tenagaKependidikanSummary', 'penggunaanDana', 'saranaLab', 'prasarana', 'tik',
            'kurikulum', 'integrasiPenelitianPkm.dosenTetap', 'pembimbinganMagang.dosenTetap',
            'kegiatanLuarKelas', 'pembimbinganTa.dosenTetap', 'ipkLulusan', 'masaStudiLulusan',
            'lulusanBekerja', 'waktuTunggu', 'kesesuaianBidang', 'kepuasanPenggunaReferensi',
            'kepuasanPenggunaKemampuan', 'penelitianPkmRingkasan', 'penelitianPkmMahasiswa',
            'publikasiDtps', 'publikasiDtpsDetail.dosenTetap', 'sitasiDtps.dosenTetap',
        ]);

        $templatePath = storage_path('app/dkps-template/template.xlsx');
        $spreadsheet = IOFactory::load($templatePath);

        $this->fillCover($spreadsheet, $dkp);
        $this->fillKerjasama($spreadsheet, $dkp);
        $this->fillKualitasInput($spreadsheet, $dkp);
        $this->fillPrestasi($spreadsheet, $dkp);
        $this->fillKaryaInovatif($spreadsheet, $dkp);
        $this->fillKepuasanMahasiswa($spreadsheet, $dkp);
        $this->fillDosenTetap($spreadsheet, $dkp);
        $this->fillBebanKerja($spreadsheet, $dkp);
        $this->fillRekognisi($spreadsheet, $dkp);
        $this->fillPengembanganKompetensi($spreadsheet, $dkp);
        $this->fillTenagaKependidikanSummary($spreadsheet, $dkp);
        $this->fillPenggunaanDana($spreadsheet, $dkp);
        $this->fillSaranaLab($spreadsheet, $dkp);
        $this->fillPrasarana($spreadsheet, $dkp);
        $this->fillTik($spreadsheet, $dkp);
        $this->fillKurikulum($spreadsheet, $dkp);
        $this->fillIntegrasi($spreadsheet, $dkp);
        $this->fillPembimbinganMagang($spreadsheet, $dkp);
        $this->fillKegiatanLuarKelas($spreadsheet, $dkp);
        $this->fillPembimbinganTa($spreadsheet, $dkp);
        $this->fillIpk($spreadsheet, $dkp);
        $this->fillMasaStudi($spreadsheet, $dkp);
        $this->fillLulusanBekerja($spreadsheet, $dkp);
        $this->fillWaktuTunggu($spreadsheet, $dkp);
        $this->fillKesesuaianBidang($spreadsheet, $dkp);
        $this->fillKepuasanPengguna($spreadsheet, $dkp);
        $this->fillPenelitianPkmRingkasan($spreadsheet, $dkp);
        $this->fillPenelitianPkmMahasiswa($spreadsheet, $dkp);
        $this->fillPublikasiDtps($spreadsheet, $dkp);
        $this->fillPublikasiDetail($spreadsheet, $dkp);
        $this->fillSitasi($spreadsheet, $dkp);

        $filename = 'DKPS-' . Str::slug($dkp->prodi->nama ?? 'prodi') . '-' . $dkp->tahun_ts_awal . '-' . $dkp->tahun_ts_akhir . '.xlsx';

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * The template ships with the source institution's real, filled-in sample
     * data (not blank). Every table we manage must have its data range wiped
     * before writing, otherwise leftover sample rows/checkboxes from the
     * original survive untouched wherever the current submission has fewer
     * rows or a null value than the sample did.
     */
    private function clearRange(Worksheet $sheet, string $startCol, string $endCol, int $startRow, int $endRow): void
    {
        for ($row = $startRow; $row <= $endRow; $row++) {
            $col = $startCol;
            while (true) {
                $sheet->setCellValue($col . $row, null);
                if ($col === $endCol) {
                    break;
                }
                $col++;
            }
        }
    }

    private function clearCells(Worksheet $sheet, array $cells): void
    {
        foreach ($cells as $cell) {
            $sheet->setCellValue($cell, null);
        }
    }

    /**
     * Writes a sequential list of records into consecutive rows starting at
     * $startRow, one row per record, up to the template's available blank rows.
     * Clears the whole [startCol,$clearEndCol] x [startRow,startRow+maxRows]
     * range first so no stale sample data survives from rows beyond this
     * submission's own record count. $clearEndCol must cover every column this
     * table touches, including any checkbox columns written outside $columnMap
     * (e.g. a "tingkat" indicator written in a separate pass) - callers with
     * such columns must pass $clearEndCol explicitly rather than relying on the
     * columnMap-derived default.
     */
    private function writeSequential(Worksheet $sheet, int $startRow, iterable $items, array $columnMap, int $maxRows = 190, ?string $clearEndCol = null): void
    {
        $cols = array_keys($columnMap);
        sort($cols);
        $endCol = $clearEndCol ?? $cols[count($cols) - 1];
        $this->clearRange($sheet, $cols[0], $endCol, $startRow, $startRow + $maxRows - 1);

        $row = $startRow;
        foreach ($items as $item) {
            if ($row >= $startRow + $maxRows) {
                break;
            }
            foreach ($columnMap as $col => $resolver) {
                $value = $resolver($item);
                if ($value !== null && $value !== '') {
                    $sheet->setCellValue($col . $row, $value);
                }
            }
            $row++;
        }
    }

    private function setIfNotNull(Worksheet $sheet, string $cell, $value): void
    {
        if ($value !== null && $value !== '') {
            $sheet->setCellValue($cell, $value);
        }
    }

    private function tingkatColumn(string $tingkat): string
    {
        return match ($tingkat) {
            'wilayah_lokal' => 'C',
            'nasional' => 'D',
            'internasional' => 'E',
            default => 'C',
        };
    }

    // ---- Cover (MENU sheet) ----

    private function fillCover(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('MENU');
        if (!$sheet) {
            return;
        }

        $this->setIfNotNull($sheet, 'H5', $dkp->prodi->nama ?? null);
        if ($dkp->akreditasi) {
            $this->setIfNotNull($sheet, 'H7', $dkp->akreditasi->peringkat);
            $this->setIfNotNull($sheet, 'H9', $dkp->akreditasi->nomor_sk);
            $this->setIfNotNull($sheet, 'H11', optional($dkp->akreditasi->tanggal_kadaluarsa)->format('Y-m-d'));
        }
        $this->setIfNotNull($sheet, 'H28', $dkp->tahun_ts_awal);
        $this->setIfNotNull($sheet, 'K28', $dkp->tahun_ts_akhir);
        $this->setIfNotNull($sheet, 'T30', $dkp->nama_pengusul);
        $this->setIfNotNull($sheet, 'T32', optional($dkp->tanggal_pengusulan)->translatedFormat('d F Y'));
    }

    // ---- Tabel 1.1 / 1.2 / 1.3 - Kerjasama ----

    private function fillKerjasama(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheetMap = ['pendidikan' => '1.1', 'penelitian' => '1.2', 'pkm' => '1.3'];

        foreach ($sheetMap as $bidang => $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (!$sheet) {
                continue;
            }

            $items = $dkp->kerjasama->where('bidang', $bidang)->values();
            $this->writeSequential($sheet, 9, $items, [
                'B' => fn ($r) => $r->lembaga_mitra,
                'F' => fn ($r) => $r->judul_kegiatan,
                'G' => fn ($r) => $r->manfaat,
                'H' => fn ($r) => optional($r->tanggal_awal)->format('d/m/Y'),
                'I' => fn ($r) => optional($r->tanggal_akhir)->format('d/m/Y'),
            ]);

            $row = 9;
            foreach ($items as $item) {
                $sheet->setCellValue($this->tingkatColumn($item->tingkat) . $row, 'V');
                $row++;
            }
        }
    }

    // ---- Tabel 2 - Kualitas Input Mahasiswa ----

    private function fillKualitasInput(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('2');
        if (!$sheet) {
            return;
        }

        $order = ['TS-4' => 6, 'TS-3' => 7, 'TS-2' => 8, 'TS-1' => 9, 'TS' => 10];
        foreach ($dkp->kualitasInput as $item) {
            $row = $order[$item->tahun_relatif] ?? null;
            if (!$row) {
                continue;
            }
            $this->setIfNotNull($sheet, "B{$row}", $item->daya_tampung);
            $this->setIfNotNull($sheet, "C{$row}", $item->pendaftar);
            $this->setIfNotNull($sheet, "D{$row}", $item->lulus_seleksi);
            $this->setIfNotNull($sheet, "E{$row}", $item->mahasiswa_baru_reguler);
            $this->setIfNotNull($sheet, "F{$row}", $item->mahasiswa_baru_transfer);
            $this->setIfNotNull($sheet, "G{$row}", $item->mahasiswa_aktif_reguler);
            $this->setIfNotNull($sheet, "H{$row}", $item->mahasiswa_aktif_transfer);
            $this->setIfNotNull($sheet, "I{$row}", $item->mahasiswa_pddikti);
        }
    }

    // ---- Tabel 3 - Prestasi Mahasiswa ----

    private function fillPrestasi(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('3');
        if (!$sheet) {
            return;
        }

        $items = $dkp->prestasiMahasiswa;
        $this->writeSequential($sheet, 9, $items, [
            'B' => fn ($r) => $r->nama_kegiatan,
            'C' => fn ($r) => $r->jenis_prestasi === 'akademik' ? 'Akademik' : 'Non-Akademik',
            'D' => fn ($r) => optional($r->tanggal_perolehan)->format('d/m/Y'),
            'H' => fn ($r) => $r->prestasi_dicapai,
        ]);

        $row = 9;
        foreach ($items as $item) {
            $col = match ($item->tingkat) {
                'wilayah_lokal' => 'E', 'nasional' => 'F', 'internasional' => 'G', default => 'E',
            };
            $sheet->setCellValue($col . $row, 'V');
            $row++;
        }
    }

    // ---- Tabel 4.1-4.4 - Karya Inovatif Mahasiswa ----

    private function fillKaryaInovatif(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $peringkatLabel = [
            'sinta_1' => 'SINTA 1', 'sinta_2' => 'SINTA 2', 'sinta_3' => 'SINTA 3', 'sinta_4' => 'SINTA 4', 'sinta_5' => 'SINTA 5',
            'jurnal_internasional' => 'Jurnal Internasional', 'jurnal_internasional_bereputasi' => 'Jurnal Internasional Bereputasi',
        ];

        $sheet41 = $spreadsheet->getSheetByName('4.1');
        if ($sheet41) {
            $this->writeSequential($sheet41, 6, $dkp->karyaInovatifMahasiswa->where('kategori', 'paten')->values(), [
                'B' => fn ($r) => $r->nim, 'C' => fn ($r) => $r->nama_mahasiswa, 'D' => fn ($r) => $r->judul,
                'E' => fn ($r) => $r->tahun, 'F' => fn ($r) => $r->keterangan,
            ]);
        }

        $sheet42 = $spreadsheet->getSheetByName('4.2');
        if ($sheet42) {
            $this->writeSequential($sheet42, 6, $dkp->karyaInovatifMahasiswa->where('kategori', 'buku_isbn')->values(), [
                'B' => fn ($r) => $r->nim, 'C' => fn ($r) => $r->nama_mahasiswa, 'D' => fn ($r) => $r->judul,
                'E' => fn ($r) => $r->tahun, 'F' => fn ($r) => $r->keterangan,
            ]);
        }

        $sheet43 = $spreadsheet->getSheetByName('4.3');
        if ($sheet43) {
            $this->writeSequential($sheet43, 6, $dkp->karyaInovatifMahasiswa->where('kategori', 'karya_seni')->values(), [
                'B' => fn ($r) => $r->nim, 'C' => fn ($r) => $r->nama_mahasiswa, 'D' => fn ($r) => $r->judul,
                'E' => fn ($r) => $r->tahun, 'F' => fn ($r) => $r->keterangan,
            ]);
        }

        $sheet44 = $spreadsheet->getSheetByName('4.4');
        if ($sheet44) {
            $this->writeSequential($sheet44, 14, $dkp->karyaInovatifMahasiswa->where('kategori', 'publikasi_jurnal')->values(), [
                'B' => fn ($r) => $r->nim, 'C' => fn ($r) => $r->nama_mahasiswa, 'D' => fn ($r) => $r->judul,
                'E' => fn ($r) => $r->keterangan,
                'F' => fn ($r) => $peringkatLabel[$r->peringkat_jurnal] ?? null,
                'G' => fn ($r) => $r->tahun, 'H' => fn ($r) => $r->tautan,
            ]);
        }
    }

    // ---- Tabel 5 - Kepuasan Mahasiswa ----

    private function fillKepuasanMahasiswa(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('5');
        if (!$sheet) {
            return;
        }

        $order = ['keandalan' => 6, 'daya_tanggap' => 7, 'kepastian' => 8, 'empati' => 9, 'tangible' => 10];
        foreach ($dkp->kepuasanMahasiswa as $item) {
            $row = $order[$item->aspek] ?? null;
            if (!$row) {
                continue;
            }
            $this->setIfNotNull($sheet, "C{$row}", $item->persen_sangat_baik);
            $this->setIfNotNull($sheet, "D{$row}", $item->persen_baik);
            $this->setIfNotNull($sheet, "E{$row}", $item->persen_cukup);
            $this->setIfNotNull($sheet, "F{$row}", $item->persen_kurang);
            $this->setIfNotNull($sheet, "G{$row}", $item->rencana_tindak_lanjut);
        }
    }

    // ---- Tabel 6 - Dosen Tetap ----

    private function fillDosenTetap(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('6');
        if (!$sheet) {
            return;
        }

        $jabatanLabel = ['tenaga_pengajar' => 'Tenaga Pengajar', 'asisten_ahli' => 'Asisten Ahli', 'lektor' => 'Lektor', 'lektor_kepala' => 'Lektor Kepala', 'guru_besar' => 'Guru Besar'];

        $this->writeSequential($sheet, 12, $dkp->dosenTetap, [
            'B' => fn ($r) => $r->nama, 'C' => fn ($r) => $r->nidn_nidk, 'D' => fn ($r) => $r->nuptk,
            'E' => fn ($r) => $r->pendidikan_magister_bidang, 'F' => fn ($r) => $r->pendidikan_doktor_bidang,
            'G' => fn ($r) => $r->bidang_keahlian, 'H' => fn ($r) => $jabatanLabel[$r->jabatan_akademik] ?? null,
            'I' => fn ($r) => $r->no_sertifikat_pendidik, 'J' => fn ($r) => $r->mk_diampu_ps_diakreditasi,
            'K' => fn ($r) => $r->mk_diampu_ps_lain,
        ], 88);
    }

    // ---- Tabel 7 - Beban Kerja DTPS ----

    private function fillBebanKerja(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('7');
        if (!$sheet) {
            return;
        }

        $this->writeSequential($sheet, 9, $dkp->bebanKerjaDtps, [
            'B' => fn ($r) => $r->dosenTetap->nama ?? null,
            'C' => fn ($r) => $r->sks_pendidikan_ps, 'D' => fn ($r) => $r->sks_pendidikan_ps_lain_dalam,
            'E' => fn ($r) => $r->sks_pendidikan_ps_lain_luar, 'F' => fn ($r) => $r->sks_penelitian,
            'G' => fn ($r) => $r->sks_pkm, 'H' => fn ($r) => $r->sks_tugas_tambahan,
        ], 88);
    }

    // ---- Tabel 8 - Rekognisi DTPS ----

    private function fillRekognisi(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('8');
        if (!$sheet) {
            return;
        }

        $jenisLabel = ['visiting_lecturer' => 'Visiting lecturer atau visiting scholar', 'keynote_speaker' => 'Keynote speaker atau invited speaker',
            'editor_mitra_bestari' => 'Editor atau mitra bestari', 'staf_ahli_narasumber' => 'Staf ahli atau narasumber',
            'penghargaan_prestasi' => 'Penghargaan atas prestasi dan kinerja'];
        $tingkatLabel = ['wilayah_lokal' => 'Lokal/Wilayah', 'nasional' => 'Nasional', 'internasional' => 'Internasional'];

        $this->writeSequential($sheet, 13, $dkp->rekognisiDtps, [
            'B' => fn ($r) => $r->dosenTetap->nama ?? null, 'C' => fn ($r) => $r->bidang_keahlian,
            'D' => fn ($r) => $r->deskripsi_rekognisi, 'E' => fn ($r) => $jenisLabel[$r->jenis_rekognisi] ?? null,
            'F' => fn ($r) => $r->tahun, 'G' => fn ($r) => $tingkatLabel[$r->tingkat] ?? null,
        ], 188);
    }

    // ---- Tabel 9 / 11 - Pengembangan Kompetensi ----

    private function fillPengembanganKompetensi(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet9 = $spreadsheet->getSheetByName('9');
        if ($sheet9) {
            $this->writeSequential($sheet9, 9, $dkp->pengembanganKompetensi->where('person_type', 'dosen')->values(), [
                'B' => fn ($r) => $r->dosenTetap->nama ?? null, 'C' => fn ($r) => $r->deskripsi_kegiatan,
                'D' => fn ($r) => $r->tempat, 'E' => fn ($r) => $r->waktu_pelaksanaan, 'F' => fn ($r) => $r->manfaat,
            ], 140);
        }

        $sheet11 = $spreadsheet->getSheetByName('11');
        if ($sheet11) {
            $this->writeSequential($sheet11, 9, $dkp->pengembanganKompetensi->where('person_type', 'tendik')->values(), [
                'B' => fn ($r) => $r->tenagaKependidikan->nama ?? null, 'C' => fn ($r) => $r->deskripsi_kegiatan,
                'D' => fn ($r) => $r->tempat, 'E' => fn ($r) => $r->waktu_pelaksanaan, 'F' => fn ($r) => $r->manfaat,
            ], 140);
        }
    }

    // ---- Tabel 10 - Tenaga Kependidikan (fixed rows) ----

    private function fillTenagaKependidikanSummary(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('10');
        if (!$sheet) {
            return;
        }

        $this->clearRange($sheet, 'C', 'J', 6, 9);

        $order = ['pustakawan' => 6, 'laboran' => 7, 'administrasi' => 8, 'lainnya' => 9];
        foreach ($dkp->tenagaKependidikanSummary as $item) {
            $row = $order[$item->jenis] ?? null;
            if (!$row) {
                continue;
            }
            $this->setIfNotNull($sheet, "C{$row}", $item->jumlah_s3);
            $this->setIfNotNull($sheet, "D{$row}", $item->jumlah_s2);
            $this->setIfNotNull($sheet, "E{$row}", $item->jumlah_s1);
            $this->setIfNotNull($sheet, "F{$row}", $item->jumlah_d4);
            $this->setIfNotNull($sheet, "G{$row}", $item->jumlah_d3);
            $this->setIfNotNull($sheet, "H{$row}", $item->jumlah_sma_smk);
            $this->setIfNotNull($sheet, "J{$row}", $item->unit_kerja);
        }
    }

    // ---- Tabel 12 - Penggunaan Dana (fixed rows) ----

    private function fillPenggunaanDana(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('12');
        if (!$sheet) {
            return;
        }

        $rowMap = [
            'biaya_operasional_pendidikan:a' => 7, 'biaya_operasional_pendidikan:b' => 8,
            'biaya_operasional_pendidikan:c' => 9, 'biaya_operasional_pendidikan:d' => 10,
            'biaya_operasional_pendidikan:e' => 11, 'operasional_penelitian:' => 13,
            'operasional_pkm:' => 14, 'investasi_sdm:' => 16, 'investasi_sarana:' => 17,
            'investasi_prasarana:' => 18,
        ];

        // Clear only the exact data cells we write - not rows 6/12/15/19 (category
        // labels and "Jumlah" subtotal formulas), and not columns G/K on the rows
        // we do touch (those hold "Rata-Rata" formulas that must survive).
        foreach (array_values($rowMap) as $row) {
            $this->clearCells($sheet, ["C{$row}", "D{$row}", "E{$row}", "F{$row}", "H{$row}", "I{$row}", "J{$row}"]);
        }

        foreach ($dkp->penggunaanDana as $item) {
            $key = $item->kategori . ':' . ($item->sub_item ?? '');
            $row = $rowMap[$key] ?? null;
            if (!$row) {
                continue;
            }
            $this->setIfNotNull($sheet, "C{$row}", $item->jenis_penggunaan);
            $this->setIfNotNull($sheet, "D{$row}", $item->up_ps_ts2);
            $this->setIfNotNull($sheet, "E{$row}", $item->up_ps_ts1);
            $this->setIfNotNull($sheet, "F{$row}", $item->up_ps_ts);
            $this->setIfNotNull($sheet, "H{$row}", $item->ps_ts2);
            $this->setIfNotNull($sheet, "I{$row}", $item->ps_ts1);
            $this->setIfNotNull($sheet, "J{$row}", $item->ps_ts);
        }
    }

    // ---- Tabel 13 - Sarana Lab ----

    private function fillSaranaLab(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('13');
        if (!$sheet) {
            return;
        }

        $kualitasLabel = ['sangat_baik' => 'Sangat Baik', 'baik' => 'Baik', 'kurang_baik' => 'Kurang Baik', 'tidak_baik' => 'Tidak Baik'];
        $kepemilikanLabel = ['milik_sendiri' => 'Milik Sendiri', 'sewa' => 'Sewa'];
        $kondisiLabel = ['terawat' => 'Terawat', 'tidak_terawat' => 'Tidak Terawat'];

        $this->writeSequential($sheet, 11, $dkp->saranaLab, [
            'B' => fn ($r) => $r->nama_lab_ruang, 'C' => fn ($r) => $r->nama_alat_peraga,
            'D' => fn ($r) => $kualitasLabel[$r->kualitas] ?? null, 'E' => fn ($r) => $r->jumlah,
            'F' => fn ($r) => $kepemilikanLabel[$r->kepemilikan] ?? null, 'G' => fn ($r) => $kondisiLabel[$r->kondisi] ?? null,
            'H' => fn ($r) => $r->rata_rata_jam_minggu,
        ]);
    }

    // ---- Tabel 14 - Prasarana ----

    private function fillPrasarana(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('14');
        if (!$sheet) {
            return;
        }

        $kualitasLabel = ['sangat_baik' => 'Sangat Baik', 'baik' => 'Baik', 'kurang_baik' => 'Kurang Baik', 'tidak_baik' => 'Tidak Baik'];
        $kepemilikanLabel = ['milik_sendiri' => 'Milik Sendiri', 'sewa' => 'Sewa'];
        $kondisiLabel = ['terawat' => 'Terawat', 'tidak_terawat' => 'Tidak Terawat'];

        $this->writeSequential($sheet, 11, $dkp->prasarana, [
            'B' => fn ($r) => $r->nama_prasarana, 'C' => fn ($r) => $r->fungsi,
            'D' => fn ($r) => $r->jumlah_unit, 'E' => fn ($r) => $r->total_luas_m2,
            'F' => fn ($r) => $kualitasLabel[$r->kualitas] ?? null, 'G' => fn ($r) => $kepemilikanLabel[$r->kepemilikan] ?? null,
            'H' => fn ($r) => $kondisiLabel[$r->kondisi] ?? null,
        ]);
    }

    // ---- Tabel 15 - TIK ----

    private function fillTik(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('15');
        if (!$sheet) {
            return;
        }

        $terintegrasiLabel = ['penuh' => 'Terintegrasi Penuh', 'sebagian' => 'Terintegrasi Sebagian', 'tidak_terintegrasi' => 'Tidak Terintegrasi'];
        $mutahirLabel = ['mutahir' => 'Mutahir', 'tidak_mutahir' => 'Tidak Mutahir'];
        $kepemilikanLabel = ['milik_sendiri' => 'Milik Sendiri', 'sewa' => 'Sewa'];
        $kondisiLabel = ['terawat' => 'Terawat', 'tidak_terawat' => 'Tidak Terawat'];

        $this->writeSequential($sheet, 10, $dkp->tik, [
            'B' => fn ($r) => $r->nama_infrastruktur, 'C' => fn ($r) => $r->deskripsi, 'D' => fn ($r) => $r->jumlah,
            'E' => fn ($r) => $terintegrasiLabel[$r->terintegrasi] ?? null, 'F' => fn ($r) => $mutahirLabel[$r->mutahir] ?? null,
            'G' => fn ($r) => $r->ada_panduan ? 'V' : null,
            'H' => fn ($r) => $kepemilikanLabel[$r->kepemilikan] ?? null, 'I' => fn ($r) => $kondisiLabel[$r->kondisi] ?? null,
        ]);
    }

    // ---- Tabel 16 - Kurikulum ----

    private function fillKurikulum(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('16');
        if (!$sheet) {
            return;
        }

        $this->writeSequential($sheet, 10, $dkp->kurikulum, [
            'B' => fn ($r) => $r->semester, 'C' => fn ($r) => $r->kode_mk, 'D' => fn ($r) => $r->nama_mk,
            'E' => fn ($r) => $r->kompetensi_inti ? 'V' : null,
            'F' => fn ($r) => $r->sks_kuliah, 'G' => fn ($r) => $r->sks_praktikum, 'H' => fn ($r) => $r->sks_praktik_lapangan,
            'I' => fn ($r) => $r->tautan_rps, 'J' => fn ($r) => $r->tautan_asesmen_cpl,
        ], 190);
    }

    // ---- Tabel 17 - Integrasi Penelitian/PkM ----

    private function fillIntegrasi(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('17');
        if (!$sheet) {
            return;
        }

        $bentukLabel = ['tambahan_materi' => 'Tambahan Materi Perkuliahan', 'studi_kasus' => 'Studi Kasus',
            'bab_buku_ajar' => 'Bab-Subab dalam Buku Ajar', 'bahan_ajar' => 'Bahan Ajar', 'bentuk_lain' => 'Bentuk Lain yang Relevan'];

        $this->writeSequential($sheet, 15, $dkp->integrasiPenelitianPkm, [
            'B' => fn ($r) => $r->dosenTetap->nama ?? null, 'C' => fn ($r) => $r->judul_penelitian_pkm,
            'D' => fn ($r) => $r->mata_kuliah, 'E' => fn ($r) => $bentukLabel[$r->bentuk_integrasi] ?? null,
        ], 185, 'H');

        $row = 15;
        foreach ($dkp->integrasiPenelitianPkm as $item) {
            $col = match ($item->tahun_relatif) { 'TS-2' => 'F', 'TS-1' => 'G', 'TS' => 'H', default => 'F' };
            $sheet->setCellValue($col . $row, 'V');
            $row++;
        }
    }

    // ---- Tabel 18 - Pembimbingan Magang ----

    private function fillPembimbinganMagang(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('18');
        if (!$sheet) {
            return;
        }

        $this->writeSequential($sheet, 8, $dkp->pembimbinganMagang, [
            'B' => fn ($r) => $r->dosenTetap->nama ?? null,
            'C' => fn ($r) => $r->jml_mhs_ts2, 'D' => fn ($r) => $r->jml_mhs_ts1, 'E' => fn ($r) => $r->jml_mhs_ts,
            'F' => fn ($r) => $r->jml_pertemuan_ts2, 'G' => fn ($r) => $r->jml_pertemuan_ts1, 'H' => fn ($r) => $r->jml_pertemuan_ts,
            'I' => fn ($r) => $r->lama_bulan,
        ], 140);
    }

    // ---- Tabel 19 - Kegiatan Luar Kelas ----

    private function fillKegiatanLuarKelas(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('19');
        if (!$sheet) {
            return;
        }

        $this->writeSequential($sheet, 10, $dkp->kegiatanLuarKelas, [
            'B' => fn ($r) => $r->nama_tema_kegiatan, 'C' => fn ($r) => $r->dosen_pembina,
            'D' => fn ($r) => optional($r->tanggal)->format('d/m/Y'), 'E' => fn ($r) => $r->tahun_relatif,
        ], 90);
    }

    // ---- Tabel 20 - Pembimbingan TA ----

    private function fillPembimbinganTa(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('20');
        if (!$sheet) {
            return;
        }

        $this->writeSequential($sheet, 9, $dkp->pembimbinganTa, [
            'B' => fn ($r) => $r->dosenTetap->nama ?? null,
            'C' => fn ($r) => $r->jml_bimbing_ps_sendiri_ts2, 'D' => fn ($r) => $r->jml_bimbing_ps_sendiri_ts1, 'E' => fn ($r) => $r->jml_bimbing_ps_sendiri_ts,
            'F' => fn ($r) => $r->jml_bimbing_ps_lain_ts2, 'G' => fn ($r) => $r->jml_bimbing_ps_lain_ts1, 'H' => fn ($r) => $r->jml_bimbing_ps_lain_ts,
            'I' => fn ($r) => $r->jml_pertemuan_ts2, 'J' => fn ($r) => $r->jml_pertemuan_ts1, 'K' => fn ($r) => $r->jml_pertemuan_ts,
        ], 90);
    }

    // ---- Tabel 21 - IPK Lulusan (fixed rows) ----

    private function fillIpk(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('21');
        if (!$sheet) {
            return;
        }

        $order = ['TS-2' => 10, 'TS-1' => 11, 'TS' => 12];
        foreach ($dkp->ipkLulusan as $item) {
            $row = $order[$item->tahun_lulus] ?? null;
            if (!$row) {
                continue;
            }
            $this->setIfNotNull($sheet, "B{$row}", $item->jumlah_lulusan);
            $this->setIfNotNull($sheet, "C{$row}", $item->ipk_min);
            $this->setIfNotNull($sheet, "D{$row}", $item->ipk_rata);
            $this->setIfNotNull($sheet, "E{$row}", $item->ipk_maks);
        }
    }

    // ---- Tabel 22 - Masa Studi Lulusan (fixed rows) ----

    private function fillMasaStudi(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('22');
        if (!$sheet) {
            return;
        }

        $order = ['TS-7' => 10, 'TS-6' => 11, 'TS-5' => 12, 'TS-4' => 13, 'TS-3' => 14];
        foreach ($dkp->masaStudiLulusan as $item) {
            $row = $order[$item->tahun_masuk] ?? null;
            if (!$row) {
                continue;
            }
            $this->setIfNotNull($sheet, "B{$row}", $item->jumlah_diterima);
            $this->setIfNotNull($sheet, "C{$row}", $item->lulus_ts7);
            $this->setIfNotNull($sheet, "D{$row}", $item->lulus_ts6);
            $this->setIfNotNull($sheet, "E{$row}", $item->lulus_ts5);
            $this->setIfNotNull($sheet, "F{$row}", $item->lulus_ts4);
            $this->setIfNotNull($sheet, "G{$row}", $item->lulus_ts3);
            $this->setIfNotNull($sheet, "H{$row}", $item->lulus_ts2);
            $this->setIfNotNull($sheet, "I{$row}", $item->lulus_ts1);
            $this->setIfNotNull($sheet, "J{$row}", $item->lulus_ts);
        }
    }

    // ---- Tabel 23 - Lulusan Bekerja (fixed rows) ----

    private function fillLulusanBekerja(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('23');
        if (!$sheet) {
            return;
        }

        $order = ['TS-4' => 10, 'TS-3' => 11, 'TS-2' => 12];
        foreach ($dkp->lulusanBekerja as $item) {
            $row = $order[$item->tahun_lulus] ?? null;
            if (!$row) {
                continue;
            }
            $this->setIfNotNull($sheet, "B{$row}", $item->jumlah_lulusan);
            $this->setIfNotNull($sheet, "C{$row}", $item->jumlah_terlacak);
            $this->setIfNotNull($sheet, "D{$row}", $item->bekerja_sesuai_bidang);
            $this->setIfNotNull($sheet, "E{$row}", $item->usaha_mandiri);
            $this->setIfNotNull($sheet, "F{$row}", $item->studi_lanjut_s2);
            $this->setIfNotNull($sheet, "G{$row}", $item->mengikuti_ppg);
        }
    }

    // ---- Tabel 24 - Waktu Tunggu (fixed rows) ----

    private function fillWaktuTunggu(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('24');
        if (!$sheet) {
            return;
        }

        $order = ['TS-4' => 10, 'TS-3' => 11, 'TS-2' => 12];
        foreach ($dkp->waktuTunggu as $item) {
            $row = $order[$item->tahun_lulus] ?? null;
            if (!$row) {
                continue;
            }
            $this->setIfNotNull($sheet, "B{$row}", $item->jumlah_lulusan);
            $this->setIfNotNull($sheet, "C{$row}", $item->jumlah_terlacak);
            $this->setIfNotNull($sheet, "D{$row}", $item->wt_kurang_6_bulan);
            $this->setIfNotNull($sheet, "E{$row}", $item->wt_6_sampai_12_bulan);
            $this->setIfNotNull($sheet, "F{$row}", $item->wt_lebih_12_bulan);
        }
    }

    // ---- Tabel 25 - Kesesuaian Bidang (fixed rows) ----

    private function fillKesesuaianBidang(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('25');
        if (!$sheet) {
            return;
        }

        $order = ['TS-4' => 10, 'TS-3' => 11, 'TS-2' => 12];
        foreach ($dkp->kesesuaianBidang as $item) {
            $row = $order[$item->tahun_lulus] ?? null;
            if (!$row) {
                continue;
            }
            $this->setIfNotNull($sheet, "B{$row}", $item->jumlah_lulusan);
            $this->setIfNotNull($sheet, "C{$row}", $item->jumlah_terlacak);
            $this->setIfNotNull($sheet, "D{$row}", $item->rendah);
            $this->setIfNotNull($sheet, "E{$row}", $item->sedang);
            $this->setIfNotNull($sheet, "F{$row}", $item->tinggi);
        }
    }

    // ---- Tabel 26 - Kepuasan Pengguna (fixed rows, two blocks) ----

    private function fillKepuasanPengguna(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('26');
        if (!$sheet) {
            return;
        }

        $refOrder = ['TS-4' => 5, 'TS-3' => 6, 'TS-2' => 7];
        foreach ($dkp->kepuasanPenggunaReferensi as $item) {
            $row = $refOrder[$item->tahun_lulus] ?? null;
            if (!$row) {
                continue;
            }
            $this->setIfNotNull($sheet, "F{$row}", $item->jumlah_lulusan);
            $this->setIfNotNull($sheet, "G{$row}", $item->jumlah_tanggapan_terlacak);
        }

        $kemampuanOrder = [
            'etika' => 13, 'keahlian_bidang_ilmu' => 14, 'bahasa_asing' => 15, 'ti' => 16, 'komunikasi' => 17,
            'kerjasama_tim' => 18, 'pengembangan_diri' => 19, 'berfikir_kritis' => 20, 'kreatifitas' => 21,
        ];
        foreach ($dkp->kepuasanPenggunaKemampuan as $item) {
            $row = $kemampuanOrder[$item->jenis_kemampuan] ?? null;
            if (!$row) {
                continue;
            }
            $this->setIfNotNull($sheet, "C{$row}", $item->persen_sangat_baik);
            $this->setIfNotNull($sheet, "D{$row}", $item->persen_baik);
            $this->setIfNotNull($sheet, "E{$row}", $item->persen_cukup);
            $this->setIfNotNull($sheet, "F{$row}", $item->persen_kurang);
            $this->setIfNotNull($sheet, "G{$row}", $item->rencana_tindak_lanjut);
        }
    }

    // ---- Tabel 27 / 32 - Penelitian/PkM Ringkasan (fixed rows) ----

    private function fillPenelitianPkmRingkasan(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $order = ['pt_mandiri' => 6, 'lembaga_dalam_negeri' => 7, 'lembaga_luar_negeri' => 8];
        $sheetMap = ['penelitian' => '27', 'pkm' => '32'];

        foreach ($sheetMap as $jenis => $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (!$sheet) {
                continue;
            }
            foreach ($dkp->penelitianPkmRingkasan->where('jenis', $jenis) as $item) {
                $row = $order[$item->sumber_pembiayaan] ?? null;
                if (!$row) {
                    continue;
                }
                $this->setIfNotNull($sheet, "C{$row}", $item->jumlah_ts2);
                $this->setIfNotNull($sheet, "D{$row}", $item->jumlah_ts1);
                $this->setIfNotNull($sheet, "E{$row}", $item->jumlah_ts);
            }
        }
    }

    // ---- Tabel 28 / 33 - Penelitian/PkM Melibatkan Mahasiswa ----

    private function fillPenelitianPkmMahasiswa(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet28 = $spreadsheet->getSheetByName('28');
        if ($sheet28) {
            $this->writeSequential($sheet28, 10, $dkp->penelitianPkmMahasiswa->where('jenis', 'penelitian')->values(), [
                'B' => fn ($r) => $r->nama_dtps, 'C' => fn ($r) => $r->judul_tema,
                'D' => fn ($r) => $r->nim_nama_mahasiswa, 'E' => fn ($r) => $r->peran_mahasiswa, 'F' => fn ($r) => $r->tahun_relatif,
            ], 190);
        }

        $sheet33 = $spreadsheet->getSheetByName('33');
        if ($sheet33) {
            $this->writeSequential($sheet33, 10, $dkp->penelitianPkmMahasiswa->where('jenis', 'pkm')->values(), [
                'B' => fn ($r) => $r->nama_dtps, 'C' => fn ($r) => $r->judul_tema,
                'D' => fn ($r) => $r->nim_nama_mahasiswa, 'E' => fn ($r) => $r->peran_mahasiswa, 'F' => fn ($r) => $r->tahun_relatif,
            ], 190);
        }
    }

    // ---- Tabel 29 - Publikasi Ilmiah DTPS (fixed rows) ----

    private function fillPublikasiDtps(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('29');
        if (!$sheet) {
            return;
        }

        $order = array_combine(
            \App\Models\DkpsPublikasiDtps::MEDIA_PUBLIKASI,
            range(6, 6 + count(\App\Models\DkpsPublikasiDtps::MEDIA_PUBLIKASI) - 1)
        );

        foreach ($dkp->publikasiDtps as $item) {
            $row = $order[$item->media_publikasi] ?? null;
            if (!$row) {
                continue;
            }
            $this->setIfNotNull($sheet, "C{$row}", $item->jumlah_ts2);
            $this->setIfNotNull($sheet, "D{$row}", $item->jumlah_ts1);
            $this->setIfNotNull($sheet, "E{$row}", $item->jumlah_ts);
        }
    }

    // ---- Tabel 30 - Publikasi DTPS Sinta/Scopus ----

    private function fillPublikasiDetail(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('30');
        if (!$sheet) {
            return;
        }

        $peranLabel = ['penulis_pertama' => 'Penulis Pertama', 'corresponding_author' => 'Corresponding author'];
        $jenisLabel = ['nasional' => 'Jurnal Nasional', 'internasional' => 'Jurnal Internasional'];
        $terindeksLabel = ['scopus_q1' => 'Scopus Q1', 'scopus_q2' => 'Scopus Q2', 'scopus_q3' => 'Scopus Q3', 'scopus_q4' => 'Scopus Q4',
            'wos' => 'WoS (Web of Science)', 'sinta_1' => 'SINTA 1', 'sinta_2' => 'SINTA 2', 'sinta_3' => 'SINTA 3', 'sinta_4' => 'SINTA 4'];

        $this->writeSequential($sheet, 17, $dkp->publikasiDtpsDetail, [
            'B' => fn ($r) => $r->dosenTetap->nama ?? null, 'C' => fn ($r) => $r->judul_artikel,
            'D' => fn ($r) => $r->nama_penulis, 'E' => fn ($r) => $peranLabel[$r->penulis_peran] ?? null,
            'F' => fn ($r) => $jenisLabel[$r->jenis_jurnal] ?? null, 'G' => fn ($r) => $terindeksLabel[$r->terindeks] ?? null,
            'H' => fn ($r) => optional($r->tanggal_terbit)->format('d/m/Y'),
        ], 180);
    }

    // ---- Tabel 31 - Sitasi DTPS ----

    private function fillSitasi(Spreadsheet $spreadsheet, DkpsSubmission $dkp): void
    {
        $sheet = $spreadsheet->getSheetByName('31');
        if (!$sheet) {
            return;
        }

        $this->writeSequential($sheet, 10, $dkp->sitasiDtps, [
            'B' => fn ($r) => $r->dosenTetap->nama ?? null, 'C' => fn ($r) => $r->judul_karya_disitasi, 'D' => fn ($r) => $r->jumlah_sitasi,
        ], 190);
    }
}
