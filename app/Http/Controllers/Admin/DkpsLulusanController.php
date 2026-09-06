<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DkpsIpkLulusan;
use App\Models\DkpsKesesuaianBidang;
use App\Models\DkpsKepuasanPenggunaKemampuan;
use App\Models\DkpsKepuasanPenggunaReferensi;
use App\Models\DkpsLulusanBekerja;
use App\Models\DkpsMasaStudiLulusan;
use App\Models\DkpsSubmission;
use App\Models\DkpsWaktuTunggu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DkpsLulusanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:dkps.manage'),
        ];
    }

    private function bulkUpdate(string $modelClass, string $table, array $rows, DkpsSubmission $dkp): void
    {
        foreach ($rows as $row) {
            $modelClass::where('id', $row['id'])
                ->where('dkps_submission_id', $dkp->id)
                ->update(collect($row)->except('id')->toArray());
        }
    }

    // ---- IPK Lulusan (Table 21) ----

    public function updateIpk(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:dkps_ipk_lulusan,id',
            'rows.*.jumlah_lulusan' => 'nullable|integer|min:0',
            'rows.*.ipk_min' => 'nullable|numeric|min:0|max:4',
            'rows.*.ipk_rata' => 'nullable|numeric|min:0|max:4',
            'rows.*.ipk_maks' => 'nullable|numeric|min:0|max:4',
        ]);

        $this->bulkUpdate(DkpsIpkLulusan::class, 'dkps_ipk_lulusan', $validated['rows'], $dkp);

        return back()->with('success', 'Data IPK lulusan berhasil disimpan.');
    }

    // ---- Masa Studi Lulusan (Table 22) ----

    public function updateMasaStudi(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:dkps_masa_studi_lulusan,id',
            'rows.*.jumlah_diterima' => 'nullable|integer|min:0',
            'rows.*.lulus_ts7' => 'nullable|integer|min:0',
            'rows.*.lulus_ts6' => 'nullable|integer|min:0',
            'rows.*.lulus_ts5' => 'nullable|integer|min:0',
            'rows.*.lulus_ts4' => 'nullable|integer|min:0',
            'rows.*.lulus_ts3' => 'nullable|integer|min:0',
            'rows.*.lulus_ts2' => 'nullable|integer|min:0',
            'rows.*.lulus_ts1' => 'nullable|integer|min:0',
            'rows.*.lulus_ts' => 'nullable|integer|min:0',
        ]);

        $this->bulkUpdate(DkpsMasaStudiLulusan::class, 'dkps_masa_studi_lulusan', $validated['rows'], $dkp);

        return back()->with('success', 'Data masa studi lulusan berhasil disimpan.');
    }

    // ---- Lulusan Bekerja & Studi Lanjut (Table 23) ----

    public function updateLulusanBekerja(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:dkps_lulusan_bekerja,id',
            'rows.*.jumlah_lulusan' => 'nullable|integer|min:0',
            'rows.*.jumlah_terlacak' => 'nullable|integer|min:0',
            'rows.*.bekerja_sesuai_bidang' => 'nullable|integer|min:0',
            'rows.*.usaha_mandiri' => 'nullable|integer|min:0',
            'rows.*.studi_lanjut_s2' => 'nullable|integer|min:0',
            'rows.*.mengikuti_ppg' => 'nullable|integer|min:0',
        ]);

        $this->bulkUpdate(DkpsLulusanBekerja::class, 'dkps_lulusan_bekerja', $validated['rows'], $dkp);

        return back()->with('success', 'Data lulusan bekerja & studi lanjut berhasil disimpan.');
    }

    // ---- Waktu Tunggu (Table 24) ----

    public function updateWaktuTunggu(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:dkps_waktu_tunggu,id',
            'rows.*.jumlah_lulusan' => 'nullable|integer|min:0',
            'rows.*.jumlah_terlacak' => 'nullable|integer|min:0',
            'rows.*.wt_kurang_6_bulan' => 'nullable|integer|min:0',
            'rows.*.wt_6_sampai_12_bulan' => 'nullable|integer|min:0',
            'rows.*.wt_lebih_12_bulan' => 'nullable|integer|min:0',
        ]);

        $this->bulkUpdate(DkpsWaktuTunggu::class, 'dkps_waktu_tunggu', $validated['rows'], $dkp);

        return back()->with('success', 'Data waktu tunggu berhasil disimpan.');
    }

    // ---- Kesesuaian Bidang Kerja (Table 25) ----

    public function updateKesesuaianBidang(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:dkps_kesesuaian_bidang,id',
            'rows.*.jumlah_lulusan' => 'nullable|integer|min:0',
            'rows.*.jumlah_terlacak' => 'nullable|integer|min:0',
            'rows.*.rendah' => 'nullable|integer|min:0',
            'rows.*.sedang' => 'nullable|integer|min:0',
            'rows.*.tinggi' => 'nullable|integer|min:0',
        ]);

        $this->bulkUpdate(DkpsKesesuaianBidang::class, 'dkps_kesesuaian_bidang', $validated['rows'], $dkp);

        return back()->with('success', 'Data kesesuaian bidang kerja berhasil disimpan.');
    }

    // ---- Kepuasan Pengguna Lulusan (Table 26, two blocks) ----

    public function updateKepuasanPengguna(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'referensi' => 'nullable|array',
            'referensi.*.id' => 'required_with:referensi|exists:dkps_kepuasan_pengguna_referensi,id',
            'referensi.*.jumlah_lulusan' => 'nullable|integer|min:0',
            'referensi.*.jumlah_tanggapan_terlacak' => 'nullable|integer|min:0',
            'kemampuan' => 'nullable|array',
            'kemampuan.*.id' => 'required_with:kemampuan|exists:dkps_kepuasan_pengguna_kemampuan,id',
            'kemampuan.*.persen_sangat_baik' => 'nullable|numeric|min:0|max:100',
            'kemampuan.*.persen_baik' => 'nullable|numeric|min:0|max:100',
            'kemampuan.*.persen_cukup' => 'nullable|numeric|min:0|max:100',
            'kemampuan.*.persen_kurang' => 'nullable|numeric|min:0|max:100',
            'kemampuan.*.rencana_tindak_lanjut' => 'nullable|string',
        ]);

        $this->bulkUpdate(DkpsKepuasanPenggunaReferensi::class, 'dkps_kepuasan_pengguna_referensi', $validated['referensi'] ?? [], $dkp);
        $this->bulkUpdate(DkpsKepuasanPenggunaKemampuan::class, 'dkps_kepuasan_pengguna_kemampuan', $validated['kemampuan'] ?? [], $dkp);

        return back()->with('success', 'Data kepuasan pengguna lulusan berhasil disimpan.');
    }
}
