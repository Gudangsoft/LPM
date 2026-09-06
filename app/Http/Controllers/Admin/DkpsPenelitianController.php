<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DkpsPenelitianPkmMahasiswa;
use App\Models\DkpsPenelitianPkmRingkasan;
use App\Models\DkpsPublikasiDtps;
use App\Models\DkpsPublikasiDtpsDetail;
use App\Models\DkpsSitasiDtps;
use App\Models\DkpsSubmission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DkpsPenelitianController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:dkps.manage'),
        ];
    }

    private function bulkUpdate(string $modelClass, array $rows, DkpsSubmission $dkp): void
    {
        foreach ($rows as $row) {
            $modelClass::where('id', $row['id'])
                ->where('dkps_submission_id', $dkp->id)
                ->update(collect($row)->except('id')->toArray());
        }
    }

    // ---- Penelitian/PkM Ringkasan (Table 27/32, fixed rows) ----

    public function updateRingkasan(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:dkps_penelitian_pkm_ringkasan,id',
            'rows.*.jumlah_ts2' => 'nullable|integer|min:0',
            'rows.*.jumlah_ts1' => 'nullable|integer|min:0',
            'rows.*.jumlah_ts' => 'nullable|integer|min:0',
        ]);

        $this->bulkUpdate(DkpsPenelitianPkmRingkasan::class, $validated['rows'], $dkp);

        return back()->with('success', 'Data ringkasan penelitian/PkM berhasil disimpan.');
    }

    // ---- Penelitian/PkM Melibatkan Mahasiswa (Table 28/33) ----

    public function storeMahasiswa(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateMahasiswa($request);
        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DkpsPenelitianPkmMahasiswa::where('dkps_submission_id', $dkp->id)
            ->where('jenis', $validated['jenis'])->max('urutan') + 1;

        DkpsPenelitianPkmMahasiswa::create($validated);

        return back()->with('success', 'Data berhasil ditambahkan.');
    }

    public function updateMahasiswa(Request $request, DkpsSubmission $dkp, DkpsPenelitianPkmMahasiswa $item)
    {
        $item->update($this->validateMahasiswa($request));

        return back()->with('success', 'Data berhasil diperbarui.');
    }

    public function destroyMahasiswa(DkpsSubmission $dkp, DkpsPenelitianPkmMahasiswa $item)
    {
        $item->delete();

        return back()->with('success', 'Data berhasil dihapus.');
    }

    private function validateMahasiswa(Request $request): array
    {
        return $request->validate([
            'jenis' => 'required|in:penelitian,pkm',
            'nama_dtps' => 'nullable|string|max:255',
            'judul_tema' => 'required|string|max:255',
            'nim_nama_mahasiswa' => 'nullable|string',
            'peran_mahasiswa' => 'nullable|string|max:255',
            'tahun_relatif' => 'required|in:TS-2,TS-1,TS',
        ]);
    }

    // ---- Publikasi Ilmiah DTPS (Table 29, fixed rows) ----

    public function updatePublikasi(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:dkps_publikasi_dtps,id',
            'rows.*.jumlah_ts2' => 'nullable|integer|min:0',
            'rows.*.jumlah_ts1' => 'nullable|integer|min:0',
            'rows.*.jumlah_ts' => 'nullable|integer|min:0',
        ]);

        $this->bulkUpdate(DkpsPublikasiDtps::class, $validated['rows'], $dkp);

        return back()->with('success', 'Data publikasi ilmiah DTPS berhasil disimpan.');
    }

    // ---- Publikasi DTPS Sinta/Scopus (Table 30) ----

    public function storeDetail(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateDetail($request);
        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DkpsPublikasiDtpsDetail::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        DkpsPublikasiDtpsDetail::create($validated);

        return back()->with('success', 'Data publikasi berhasil ditambahkan.');
    }

    public function updateDetail(Request $request, DkpsSubmission $dkp, DkpsPublikasiDtpsDetail $detail)
    {
        $detail->update($this->validateDetail($request));

        return back()->with('success', 'Data publikasi berhasil diperbarui.');
    }

    public function destroyDetail(DkpsSubmission $dkp, DkpsPublikasiDtpsDetail $detail)
    {
        $detail->delete();

        return back()->with('success', 'Data publikasi berhasil dihapus.');
    }

    private function validateDetail(Request $request): array
    {
        return $request->validate([
            'dosen_tetap_id' => 'nullable|exists:dosen_tetap,id',
            'judul_artikel' => 'required|string',
            'nama_penulis' => 'nullable|string|max:255',
            'penulis_peran' => 'required|in:penulis_pertama,corresponding_author',
            'jenis_jurnal' => 'required|in:nasional,internasional',
            'terindeks' => 'required|in:scopus_q1,scopus_q2,scopus_q3,scopus_q4,wos,sinta_1,sinta_2,sinta_3,sinta_4',
            'tanggal_terbit' => 'nullable|date',
        ]);
    }

    // ---- Karya Ilmiah DTPS yang Disitasi (Table 31) ----

    public function storeSitasi(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateSitasi($request);
        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DkpsSitasiDtps::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        DkpsSitasiDtps::create($validated);

        return back()->with('success', 'Data sitasi berhasil ditambahkan.');
    }

    public function updateSitasi(Request $request, DkpsSubmission $dkp, DkpsSitasiDtps $sitasi)
    {
        $sitasi->update($this->validateSitasi($request));

        return back()->with('success', 'Data sitasi berhasil diperbarui.');
    }

    public function destroySitasi(DkpsSubmission $dkp, DkpsSitasiDtps $sitasi)
    {
        $sitasi->delete();

        return back()->with('success', 'Data sitasi berhasil dihapus.');
    }

    private function validateSitasi(Request $request): array
    {
        return $request->validate([
            'dosen_tetap_id' => 'nullable|exists:dosen_tetap,id',
            'judul_karya_disitasi' => 'required|string',
            'jumlah_sitasi' => 'nullable|integer|min:0',
        ]);
    }
}
