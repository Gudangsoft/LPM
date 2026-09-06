<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DkpsBebanKerjaDtps;
use App\Models\DkpsPengembanganKompetensi;
use App\Models\DkpsRekognisiDtps;
use App\Models\DkpsSubmission;
use App\Models\DkpsTenagaKependidikanSummary;
use App\Models\DosenTetap;
use App\Models\TenagaKependidikan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class DkpsDosenController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:dkps.manage'),
        ];
    }

    // ---- Dosen Tetap roster (Table 6) ----

    public function storeDosen(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateDosen($request);
        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DosenTetap::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        DosenTetap::create($validated);

        return back()->with('success', 'Data dosen tetap berhasil ditambahkan.');
    }

    public function updateDosen(Request $request, DkpsSubmission $dkp, DosenTetap $dosen)
    {
        $dosen->update($this->validateDosen($request));

        return back()->with('success', 'Data dosen tetap berhasil diperbarui.');
    }

    public function destroyDosen(DkpsSubmission $dkp, DosenTetap $dosen)
    {
        $dosen->delete();

        return back()->with('success', 'Data dosen tetap berhasil dihapus.');
    }

    private function validateDosen(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:255',
            'nidn_nidk' => 'nullable|string|max:50',
            'nuptk' => 'nullable|string|max:50',
            'pendidikan_magister_bidang' => 'nullable|string|max:255',
            'pendidikan_doktor_bidang' => 'nullable|string|max:255',
            'bidang_keahlian' => 'nullable|string|max:255',
            'jabatan_akademik' => 'nullable|in:tenaga_pengajar,asisten_ahli,lektor,lektor_kepala,guru_besar',
            'no_sertifikat_pendidik' => 'nullable|string|max:100',
            'mk_diampu_ps_diakreditasi' => 'nullable|string',
            'mk_diampu_ps_lain' => 'nullable|string',
        ]);
    }

    // ---- Beban Kerja DTPS (Table 7) ----

    public function storeBebanKerja(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateBebanKerja($request);
        $validated['dkps_submission_id'] = $dkp->id;

        DkpsBebanKerjaDtps::create($validated);

        return back()->with('success', 'Data beban kerja DTPS berhasil ditambahkan.');
    }

    public function updateBebanKerja(Request $request, DkpsSubmission $dkp, DkpsBebanKerjaDtps $beban)
    {
        $beban->update($this->validateBebanKerja($request));

        return back()->with('success', 'Data beban kerja DTPS berhasil diperbarui.');
    }

    public function destroyBebanKerja(DkpsSubmission $dkp, DkpsBebanKerjaDtps $beban)
    {
        $beban->delete();

        return back()->with('success', 'Data beban kerja DTPS berhasil dihapus.');
    }

    private function validateBebanKerja(Request $request): array
    {
        return $request->validate([
            'dosen_tetap_id' => 'required|exists:dosen_tetap,id',
            'sks_pendidikan_ps' => 'nullable|numeric|min:0',
            'sks_pendidikan_ps_lain_dalam' => 'nullable|numeric|min:0',
            'sks_pendidikan_ps_lain_luar' => 'nullable|numeric|min:0',
            'sks_penelitian' => 'nullable|numeric|min:0',
            'sks_pkm' => 'nullable|numeric|min:0',
            'sks_tugas_tambahan' => 'nullable|numeric|min:0',
        ]);
    }

    // ---- Rekognisi DTPS (Table 8) ----

    public function storeRekognisi(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateRekognisi($request);
        $validated['dkps_submission_id'] = $dkp->id;

        if ($request->hasFile('bukti_file')) {
            $validated['bukti_file'] = $request->file('bukti_file')->store('dkps/rekognisi', 'public');
        }

        DkpsRekognisiDtps::create($validated);

        return back()->with('success', 'Data rekognisi DTPS berhasil ditambahkan.');
    }

    public function updateRekognisi(Request $request, DkpsSubmission $dkp, DkpsRekognisiDtps $rekognisi)
    {
        $validated = $this->validateRekognisi($request);

        if ($request->hasFile('bukti_file')) {
            if ($rekognisi->bukti_file) {
                Storage::disk('public')->delete($rekognisi->bukti_file);
            }
            $validated['bukti_file'] = $request->file('bukti_file')->store('dkps/rekognisi', 'public');
        }

        $rekognisi->update($validated);

        return back()->with('success', 'Data rekognisi DTPS berhasil diperbarui.');
    }

    public function destroyRekognisi(DkpsSubmission $dkp, DkpsRekognisiDtps $rekognisi)
    {
        if ($rekognisi->bukti_file) {
            Storage::disk('public')->delete($rekognisi->bukti_file);
        }
        $rekognisi->delete();

        return back()->with('success', 'Data rekognisi DTPS berhasil dihapus.');
    }

    private function validateRekognisi(Request $request): array
    {
        return $request->validate([
            'dosen_tetap_id' => 'required|exists:dosen_tetap,id',
            'bidang_keahlian' => 'nullable|string|max:255',
            'deskripsi_rekognisi' => 'required|string|max:255',
            'jenis_rekognisi' => 'required|in:visiting_lecturer,keynote_speaker,editor_mitra_bestari,staf_ahli_narasumber,penghargaan_prestasi',
            'tahun' => 'nullable|integer|min:2000|max:2100',
            'tingkat' => 'required|in:wilayah_lokal,nasional,internasional',
            'bukti_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);
    }

    // ---- Pengembangan Kompetensi (Table 9 dosen / Table 11 tendik) ----

    public function storePengembangan(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validatePengembangan($request);
        $validated['dkps_submission_id'] = $dkp->id;

        if ($request->hasFile('bukti_file')) {
            $validated['bukti_file'] = $request->file('bukti_file')->store('dkps/pengembangan', 'public');
        }

        DkpsPengembanganKompetensi::create($validated);

        return back()->with('success', 'Data pengembangan kompetensi berhasil ditambahkan.');
    }

    public function updatePengembangan(Request $request, DkpsSubmission $dkp, DkpsPengembanganKompetensi $pengembangan)
    {
        $validated = $this->validatePengembangan($request);

        if ($request->hasFile('bukti_file')) {
            if ($pengembangan->bukti_file) {
                Storage::disk('public')->delete($pengembangan->bukti_file);
            }
            $validated['bukti_file'] = $request->file('bukti_file')->store('dkps/pengembangan', 'public');
        }

        $pengembangan->update($validated);

        return back()->with('success', 'Data pengembangan kompetensi berhasil diperbarui.');
    }

    public function destroyPengembangan(DkpsSubmission $dkp, DkpsPengembanganKompetensi $pengembangan)
    {
        if ($pengembangan->bukti_file) {
            Storage::disk('public')->delete($pengembangan->bukti_file);
        }
        $pengembangan->delete();

        return back()->with('success', 'Data pengembangan kompetensi berhasil dihapus.');
    }

    private function validatePengembangan(Request $request): array
    {
        $validated = $request->validate([
            'person_type' => 'required|in:dosen,tendik',
            'dosen_tetap_id' => 'nullable|required_if:person_type,dosen|exists:dosen_tetap,id',
            'tenaga_kependidikan_id' => 'nullable|required_if:person_type,tendik|exists:tenaga_kependidikan,id',
            'deskripsi_kegiatan' => 'required|string|max:255',
            'tempat' => 'nullable|string|max:255',
            'waktu_pelaksanaan' => 'nullable|string|max:100',
            'manfaat' => 'nullable|string',
            'bukti_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($validated['person_type'] === 'dosen') {
            $validated['tenaga_kependidikan_id'] = null;
        } else {
            $validated['dosen_tetap_id'] = null;
        }

        return $validated;
    }

    // ---- Tenaga Kependidikan roster (for Table 11's FK) ----

    public function storeTendik(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateTendik($request);
        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = TenagaKependidikan::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        TenagaKependidikan::create($validated);

        return back()->with('success', 'Data tenaga kependidikan berhasil ditambahkan.');
    }

    public function updateTendik(Request $request, DkpsSubmission $dkp, TenagaKependidikan $tendik)
    {
        $tendik->update($this->validateTendik($request));

        return back()->with('success', 'Data tenaga kependidikan berhasil diperbarui.');
    }

    public function destroyTendik(DkpsSubmission $dkp, TenagaKependidikan $tendik)
    {
        $tendik->delete();

        return back()->with('success', 'Data tenaga kependidikan berhasil dihapus.');
    }

    private function validateTendik(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:pustakawan,laboran,administrasi,lainnya',
        ]);
    }

    // ---- Tenaga Kependidikan Summary (Table 10, fixed rows) ----

    public function updateTendikSummary(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:dkps_tenaga_kependidikan_summary,id',
            'rows.*.jumlah_s3' => 'nullable|integer|min:0',
            'rows.*.jumlah_s2' => 'nullable|integer|min:0',
            'rows.*.jumlah_s1' => 'nullable|integer|min:0',
            'rows.*.jumlah_d4' => 'nullable|integer|min:0',
            'rows.*.jumlah_d3' => 'nullable|integer|min:0',
            'rows.*.jumlah_sma_smk' => 'nullable|integer|min:0',
            'rows.*.unit_kerja' => 'nullable|string|max:255',
        ]);

        foreach ($validated['rows'] as $row) {
            DkpsTenagaKependidikanSummary::where('id', $row['id'])
                ->where('dkps_submission_id', $dkp->id)
                ->update(collect($row)->except('id')->toArray());
        }

        return back()->with('success', 'Data tenaga kependidikan berhasil disimpan.');
    }
}
