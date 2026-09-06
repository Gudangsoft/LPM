<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DkpsKaryaInovatifMahasiswa;
use App\Models\DkpsKepuasanMahasiswa;
use App\Models\DkpsKerjasama;
use App\Models\DkpsKualitasInput;
use App\Models\DkpsPrestasiMahasiswa;
use App\Models\DkpsSubmission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class DkpsMahasiswaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:dkps.manage'),
        ];
    }

    // ---- Kerjasama (Table 1.1-1.3) ----

    public function storeKerjasama(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'bidang' => 'required|in:pendidikan,penelitian,pkm',
            'lembaga_mitra' => 'required|string|max:255',
            'tingkat' => 'required|in:wilayah_lokal,nasional,internasional',
            'judul_kegiatan' => 'required|string|max:255',
            'manfaat' => 'nullable|string',
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date',
            'bukti_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('bukti_file')) {
            $validated['bukti_file'] = $request->file('bukti_file')->store('dkps/kerjasama', 'public');
        }

        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DkpsKerjasama::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        DkpsKerjasama::create($validated);

        return back()->with('success', 'Data kerjasama berhasil ditambahkan.');
    }

    public function updateKerjasama(Request $request, DkpsSubmission $dkp, DkpsKerjasama $kerjasama)
    {
        $validated = $request->validate([
            'bidang' => 'required|in:pendidikan,penelitian,pkm',
            'lembaga_mitra' => 'required|string|max:255',
            'tingkat' => 'required|in:wilayah_lokal,nasional,internasional',
            'judul_kegiatan' => 'required|string|max:255',
            'manfaat' => 'nullable|string',
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date',
            'bukti_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('bukti_file')) {
            if ($kerjasama->bukti_file) {
                Storage::disk('public')->delete($kerjasama->bukti_file);
            }
            $validated['bukti_file'] = $request->file('bukti_file')->store('dkps/kerjasama', 'public');
        }

        $kerjasama->update($validated);

        return back()->with('success', 'Data kerjasama berhasil diperbarui.');
    }

    public function destroyKerjasama(DkpsSubmission $dkp, DkpsKerjasama $kerjasama)
    {
        if ($kerjasama->bukti_file) {
            Storage::disk('public')->delete($kerjasama->bukti_file);
        }
        $kerjasama->delete();

        return back()->with('success', 'Data kerjasama berhasil dihapus.');
    }

    // ---- Kualitas Input Mahasiswa (Table 2, fixed rows) ----

    public function updateKualitasInput(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:dkps_kualitas_input,id',
            'rows.*.daya_tampung' => 'nullable|integer|min:0',
            'rows.*.pendaftar' => 'nullable|integer|min:0',
            'rows.*.lulus_seleksi' => 'nullable|integer|min:0',
            'rows.*.mahasiswa_baru_reguler' => 'nullable|integer|min:0',
            'rows.*.mahasiswa_baru_transfer' => 'nullable|integer|min:0',
            'rows.*.mahasiswa_aktif_reguler' => 'nullable|integer|min:0',
            'rows.*.mahasiswa_aktif_transfer' => 'nullable|integer|min:0',
            'rows.*.mahasiswa_pddikti' => 'nullable|integer|min:0',
        ]);

        foreach ($validated['rows'] as $row) {
            DkpsKualitasInput::where('id', $row['id'])
                ->where('dkps_submission_id', $dkp->id)
                ->update(collect($row)->except('id')->toArray());
        }

        return back()->with('success', 'Data kualitas input mahasiswa berhasil disimpan.');
    }

    // ---- Prestasi Mahasiswa (Table 3) ----

    public function storePrestasi(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_prestasi' => 'required|in:akademik,non_akademik',
            'tanggal_perolehan' => 'nullable|date',
            'tingkat' => 'required|in:wilayah_lokal,nasional,internasional',
            'prestasi_dicapai' => 'nullable|string|max:255',
        ]);

        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DkpsPrestasiMahasiswa::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        DkpsPrestasiMahasiswa::create($validated);

        return back()->with('success', 'Data prestasi mahasiswa berhasil ditambahkan.');
    }

    public function updatePrestasi(Request $request, DkpsSubmission $dkp, DkpsPrestasiMahasiswa $prestasi)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_prestasi' => 'required|in:akademik,non_akademik',
            'tanggal_perolehan' => 'nullable|date',
            'tingkat' => 'required|in:wilayah_lokal,nasional,internasional',
            'prestasi_dicapai' => 'nullable|string|max:255',
        ]);

        $prestasi->update($validated);

        return back()->with('success', 'Data prestasi mahasiswa berhasil diperbarui.');
    }

    public function destroyPrestasi(DkpsSubmission $dkp, DkpsPrestasiMahasiswa $prestasi)
    {
        $prestasi->delete();

        return back()->with('success', 'Data prestasi mahasiswa berhasil dihapus.');
    }

    // ---- Karya Inovatif Mahasiswa (Table 4.1-4.4) ----

    public function storeKaryaInovatif(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateKaryaInovatif($request);

        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DkpsKaryaInovatifMahasiswa::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        DkpsKaryaInovatifMahasiswa::create($validated);

        return back()->with('success', 'Data karya inovatif mahasiswa berhasil ditambahkan.');
    }

    public function updateKaryaInovatif(Request $request, DkpsSubmission $dkp, DkpsKaryaInovatifMahasiswa $karya)
    {
        $validated = $this->validateKaryaInovatif($request);

        $karya->update($validated);

        return back()->with('success', 'Data karya inovatif mahasiswa berhasil diperbarui.');
    }

    public function destroyKaryaInovatif(DkpsSubmission $dkp, DkpsKaryaInovatifMahasiswa $karya)
    {
        $karya->delete();

        return back()->with('success', 'Data karya inovatif mahasiswa berhasil dihapus.');
    }

    private function validateKaryaInovatif(Request $request): array
    {
        return $request->validate([
            'kategori' => 'required|in:paten,buku_isbn,karya_seni,publikasi_jurnal',
            'nim' => 'nullable|string|max:50',
            'nama_mahasiswa' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'tahun' => 'nullable|integer|min:2000|max:2100',
            'keterangan' => 'nullable|string|max:255',
            'peringkat_jurnal' => 'nullable|in:sinta_1,sinta_2,sinta_3,sinta_4,sinta_5,jurnal_internasional,jurnal_internasional_bereputasi',
            'tautan' => 'nullable|string|max:255',
        ]);
    }

    // ---- Kepuasan Mahasiswa (Table 5, fixed rows) ----

    public function updateKepuasan(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:dkps_kepuasan_mahasiswa,id',
            'rows.*.persen_sangat_baik' => 'nullable|numeric|min:0|max:100',
            'rows.*.persen_baik' => 'nullable|numeric|min:0|max:100',
            'rows.*.persen_cukup' => 'nullable|numeric|min:0|max:100',
            'rows.*.persen_kurang' => 'nullable|numeric|min:0|max:100',
            'rows.*.rencana_tindak_lanjut' => 'nullable|string',
        ]);

        foreach ($validated['rows'] as $row) {
            DkpsKepuasanMahasiswa::where('id', $row['id'])
                ->where('dkps_submission_id', $dkp->id)
                ->update(collect($row)->except('id')->toArray());
        }

        return back()->with('success', 'Data kepuasan mahasiswa berhasil disimpan.');
    }
}
