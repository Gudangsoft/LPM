<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DkpsIntegrasiPenelitianPkm;
use App\Models\DkpsKegiatanLuarKelas;
use App\Models\DkpsKurikulum;
use App\Models\DkpsPembimbinganMagang;
use App\Models\DkpsPembimbinganTa;
use App\Models\DkpsSubmission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class DkpsKurikulumController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:dkps.manage'),
        ];
    }

    // ---- Kurikulum (Table 16) ----

    public function storeKurikulum(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateKurikulum($request);
        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DkpsKurikulum::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        DkpsKurikulum::create($validated);

        return back()->with('success', 'Data mata kuliah berhasil ditambahkan.');
    }

    public function updateKurikulum(Request $request, DkpsSubmission $dkp, DkpsKurikulum $kurikulum)
    {
        $kurikulum->update($this->validateKurikulum($request));

        return back()->with('success', 'Data mata kuliah berhasil diperbarui.');
    }

    public function destroyKurikulum(DkpsSubmission $dkp, DkpsKurikulum $kurikulum)
    {
        $kurikulum->delete();

        return back()->with('success', 'Data mata kuliah berhasil dihapus.');
    }

    private function validateKurikulum(Request $request): array
    {
        $validated = $request->validate([
            'semester' => 'required|in:I,II,III,IV,V,VI,VII,VIII',
            'kode_mk' => 'nullable|string|max:50',
            'nama_mk' => 'required|string|max:255',
            'kompetensi_inti' => 'nullable|boolean',
            'sks_kuliah' => 'nullable|numeric|min:0',
            'sks_praktikum' => 'nullable|numeric|min:0',
            'sks_praktik_lapangan' => 'nullable|numeric|min:0',
            'tautan_rps' => 'nullable|string|max:255',
            'tautan_asesmen_cpl' => 'nullable|string|max:255',
        ]);

        $validated['kompetensi_inti'] = $request->boolean('kompetensi_inti');

        return $validated;
    }

    // ---- Integrasi Penelitian/PkM (Table 17) ----

    public function storeIntegrasi(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateIntegrasi($request);
        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DkpsIntegrasiPenelitianPkm::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        if ($request->hasFile('bukti_file')) {
            $validated['bukti_file'] = $request->file('bukti_file')->store('dkps/integrasi', 'public');
        }

        DkpsIntegrasiPenelitianPkm::create($validated);

        return back()->with('success', 'Data integrasi penelitian/PkM berhasil ditambahkan.');
    }

    public function updateIntegrasi(Request $request, DkpsSubmission $dkp, DkpsIntegrasiPenelitianPkm $integrasi)
    {
        $validated = $this->validateIntegrasi($request);

        if ($request->hasFile('bukti_file')) {
            if ($integrasi->bukti_file) {
                Storage::disk('public')->delete($integrasi->bukti_file);
            }
            $validated['bukti_file'] = $request->file('bukti_file')->store('dkps/integrasi', 'public');
        }

        $integrasi->update($validated);

        return back()->with('success', 'Data integrasi penelitian/PkM berhasil diperbarui.');
    }

    public function destroyIntegrasi(DkpsSubmission $dkp, DkpsIntegrasiPenelitianPkm $integrasi)
    {
        if ($integrasi->bukti_file) {
            Storage::disk('public')->delete($integrasi->bukti_file);
        }
        $integrasi->delete();

        return back()->with('success', 'Data integrasi penelitian/PkM berhasil dihapus.');
    }

    private function validateIntegrasi(Request $request): array
    {
        return $request->validate([
            'dosen_tetap_id' => 'nullable|exists:dosen_tetap,id',
            'judul_penelitian_pkm' => 'required|string|max:255',
            'mata_kuliah' => 'nullable|string|max:255',
            'bentuk_integrasi' => 'required|in:tambahan_materi,studi_kasus,bab_buku_ajar,bahan_ajar,bentuk_lain',
            'tahun_relatif' => 'required|in:TS-2,TS-1,TS',
            'bukti_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);
    }

    // ---- Pembimbingan Magang Kependidikan (Table 18) ----

    public function storePembimbinganMagang(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validatePembimbinganMagang($request);
        $validated['dkps_submission_id'] = $dkp->id;

        DkpsPembimbinganMagang::create($validated);

        return back()->with('success', 'Data pembimbingan magang berhasil ditambahkan.');
    }

    public function updatePembimbinganMagang(Request $request, DkpsSubmission $dkp, DkpsPembimbinganMagang $magang)
    {
        $magang->update($this->validatePembimbinganMagang($request));

        return back()->with('success', 'Data pembimbingan magang berhasil diperbarui.');
    }

    public function destroyPembimbinganMagang(DkpsSubmission $dkp, DkpsPembimbinganMagang $magang)
    {
        $magang->delete();

        return back()->with('success', 'Data pembimbingan magang berhasil dihapus.');
    }

    private function validatePembimbinganMagang(Request $request): array
    {
        return $request->validate([
            'dosen_tetap_id' => 'required|exists:dosen_tetap,id',
            'jml_mhs_ts2' => 'nullable|integer|min:0',
            'jml_mhs_ts1' => 'nullable|integer|min:0',
            'jml_mhs_ts' => 'nullable|integer|min:0',
            'jml_pertemuan_ts2' => 'nullable|integer|min:0',
            'jml_pertemuan_ts1' => 'nullable|integer|min:0',
            'jml_pertemuan_ts' => 'nullable|integer|min:0',
            'lama_bulan' => 'nullable|numeric|min:0',
        ]);
    }

    // ---- Kegiatan Akademik di Luar Kelas (Table 19) ----

    public function storeKegiatanLuarKelas(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateKegiatanLuarKelas($request);
        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DkpsKegiatanLuarKelas::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        if ($request->hasFile('bukti_file')) {
            $validated['bukti_file'] = $request->file('bukti_file')->store('dkps/kegiatan-luar-kelas', 'public');
        }

        DkpsKegiatanLuarKelas::create($validated);

        return back()->with('success', 'Data kegiatan akademik berhasil ditambahkan.');
    }

    public function updateKegiatanLuarKelas(Request $request, DkpsSubmission $dkp, DkpsKegiatanLuarKelas $kegiatan)
    {
        $validated = $this->validateKegiatanLuarKelas($request);

        if ($request->hasFile('bukti_file')) {
            if ($kegiatan->bukti_file) {
                Storage::disk('public')->delete($kegiatan->bukti_file);
            }
            $validated['bukti_file'] = $request->file('bukti_file')->store('dkps/kegiatan-luar-kelas', 'public');
        }

        $kegiatan->update($validated);

        return back()->with('success', 'Data kegiatan akademik berhasil diperbarui.');
    }

    public function destroyKegiatanLuarKelas(DkpsSubmission $dkp, DkpsKegiatanLuarKelas $kegiatan)
    {
        if ($kegiatan->bukti_file) {
            Storage::disk('public')->delete($kegiatan->bukti_file);
        }
        $kegiatan->delete();

        return back()->with('success', 'Data kegiatan akademik berhasil dihapus.');
    }

    private function validateKegiatanLuarKelas(Request $request): array
    {
        return $request->validate([
            'nama_tema_kegiatan' => 'required|string|max:255',
            'dosen_pembina' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'tahun_relatif' => 'required|in:TS-2,TS-1,TS',
            'bukti_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);
    }

    // ---- Pembimbingan Tugas Akhir/Skripsi (Table 20) ----

    public function storePembimbinganTa(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validatePembimbinganTa($request);
        $validated['dkps_submission_id'] = $dkp->id;

        DkpsPembimbinganTa::create($validated);

        return back()->with('success', 'Data pembimbingan tugas akhir berhasil ditambahkan.');
    }

    public function updatePembimbinganTa(Request $request, DkpsSubmission $dkp, DkpsPembimbinganTa $ta)
    {
        $ta->update($this->validatePembimbinganTa($request));

        return back()->with('success', 'Data pembimbingan tugas akhir berhasil diperbarui.');
    }

    public function destroyPembimbinganTa(DkpsSubmission $dkp, DkpsPembimbinganTa $ta)
    {
        $ta->delete();

        return back()->with('success', 'Data pembimbingan tugas akhir berhasil dihapus.');
    }

    private function validatePembimbinganTa(Request $request): array
    {
        return $request->validate([
            'dosen_tetap_id' => 'required|exists:dosen_tetap,id',
            'jml_bimbing_ps_sendiri_ts2' => 'nullable|integer|min:0',
            'jml_bimbing_ps_sendiri_ts1' => 'nullable|integer|min:0',
            'jml_bimbing_ps_sendiri_ts' => 'nullable|integer|min:0',
            'jml_bimbing_ps_lain_ts2' => 'nullable|integer|min:0',
            'jml_bimbing_ps_lain_ts1' => 'nullable|integer|min:0',
            'jml_bimbing_ps_lain_ts' => 'nullable|integer|min:0',
            'jml_pertemuan_ts2' => 'nullable|integer|min:0',
            'jml_pertemuan_ts1' => 'nullable|integer|min:0',
            'jml_pertemuan_ts' => 'nullable|integer|min:0',
        ]);
    }
}
