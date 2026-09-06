<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DkpsIpkLulusan;
use App\Models\DkpsKepuasanMahasiswa;
use App\Models\DkpsKepuasanPenggunaKemampuan;
use App\Models\DkpsKepuasanPenggunaReferensi;
use App\Models\DkpsKesesuaianBidang;
use App\Models\DkpsKualitasInput;
use App\Models\DkpsLulusanBekerja;
use App\Models\DkpsMasaStudiLulusan;
use App\Models\DkpsPenelitianPkmRingkasan;
use App\Models\DkpsPenggunaanDana;
use App\Models\DkpsPublikasiDtps;
use App\Models\DkpsSubmission;
use App\Models\DkpsTenagaKependidikanSummary;
use App\Models\DkpsWaktuTunggu;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DkpsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:dkps.view', only: ['index', 'show']),
            new Middleware('permission:dkps.manage', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $user = auth()->user();
        $submissions = DkpsSubmission::with('prodi')->latest()
            ->when(!$user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->ownedByKaprodi($user))
            ->paginate(15);

        return view('admin.dkps.index', compact('submissions'));
    }

    /**
     * Prodi options for the create/edit dropdowns - a kaprodi may only ever
     * pick the prodi (or prodi) they head; everyone else sees every active prodi.
     */
    private function scopedProdiOptions()
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $user->isKaprodi()) {
            return $user->prodiDikepalai()->active()->orderBy('nama')->get();
        }

        return Prodi::active()->orderBy('nama')->get();
    }

    public function create()
    {
        $prodis = $this->scopedProdiOptions();

        return view('admin.dkps.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prodi_id' => 'required|exists:prodi,id',
            'akreditasi_id' => 'nullable|exists:akreditasi,id',
            'tahun_ts_awal' => 'required|integer|min:2000|max:2100',
            'tahun_ts_akhir' => 'required|integer|min:2000|max:2100',
            'nama_pengusul' => 'nullable|string|max:255',
            'tanggal_pengusulan' => 'nullable|date',
        ]);

        $user = auth()->user();
        if (!$user->isAdmin() && $user->isKaprodi()) {
            abort_unless(
                $user->prodiDikepalai()->where('id', $validated['prodi_id'])->exists(),
                403,
                'Anda hanya dapat membuat draft DKPS untuk program studi Anda sendiri.'
            );
        }

        $submission = DkpsSubmission::create($validated);

        $this->seedFixedRows($submission);

        return redirect()->route('admin.dkps.show', $submission)
            ->with('success', 'Draft DKPS berhasil dibuat.');
    }

    /**
     * Several DKPS tables have fixed cardinality (e.g. exactly one row per
     * TS-4..TS academic year) rather than free "add row" entries - seed
     * those empty rows once when a submission is created.
     */
    private function seedFixedRows(DkpsSubmission $submission): void
    {
        foreach (DkpsKualitasInput::TAHUN_RELATIF as $tahun) {
            DkpsKualitasInput::create([
                'dkps_submission_id' => $submission->id,
                'tahun_relatif' => $tahun,
            ]);
        }

        foreach (DkpsKepuasanMahasiswa::ASPEK as $aspek) {
            DkpsKepuasanMahasiswa::create([
                'dkps_submission_id' => $submission->id,
                'aspek' => $aspek,
            ]);
        }

        foreach (DkpsTenagaKependidikanSummary::JENIS as $jenis) {
            DkpsTenagaKependidikanSummary::create([
                'dkps_submission_id' => $submission->id,
                'jenis' => $jenis,
            ]);
        }

        $urutan = 0;
        foreach (['a', 'b', 'c', 'd', 'e'] as $subItem) {
            DkpsPenggunaanDana::create([
                'dkps_submission_id' => $submission->id,
                'kategori' => 'biaya_operasional_pendidikan',
                'sub_item' => $subItem,
                'urutan' => $urutan++,
            ]);
        }
        foreach (['operasional_penelitian', 'operasional_pkm', 'investasi_sdm', 'investasi_sarana', 'investasi_prasarana'] as $kategori) {
            DkpsPenggunaanDana::create([
                'dkps_submission_id' => $submission->id,
                'kategori' => $kategori,
                'urutan' => $urutan++,
            ]);
        }

        foreach (DkpsIpkLulusan::TAHUN_LULUS as $tahun) {
            DkpsIpkLulusan::create(['dkps_submission_id' => $submission->id, 'tahun_lulus' => $tahun]);
        }

        foreach (DkpsMasaStudiLulusan::TAHUN_MASUK as $tahun) {
            DkpsMasaStudiLulusan::create(['dkps_submission_id' => $submission->id, 'tahun_masuk' => $tahun]);
        }

        foreach (DkpsLulusanBekerja::TAHUN_LULUS as $tahun) {
            DkpsLulusanBekerja::create(['dkps_submission_id' => $submission->id, 'tahun_lulus' => $tahun]);
        }

        foreach (DkpsWaktuTunggu::TAHUN_LULUS as $tahun) {
            DkpsWaktuTunggu::create(['dkps_submission_id' => $submission->id, 'tahun_lulus' => $tahun]);
        }

        foreach (DkpsKesesuaianBidang::TAHUN_LULUS as $tahun) {
            DkpsKesesuaianBidang::create(['dkps_submission_id' => $submission->id, 'tahun_lulus' => $tahun]);
        }

        foreach (DkpsKepuasanPenggunaReferensi::TAHUN_LULUS as $tahun) {
            DkpsKepuasanPenggunaReferensi::create(['dkps_submission_id' => $submission->id, 'tahun_lulus' => $tahun]);
        }

        $kemampuanUrutan = 0;
        foreach (DkpsKepuasanPenggunaKemampuan::JENIS_KEMAMPUAN as $jenis) {
            DkpsKepuasanPenggunaKemampuan::create([
                'dkps_submission_id' => $submission->id,
                'jenis_kemampuan' => $jenis,
                'urutan' => $kemampuanUrutan++,
            ]);
        }

        foreach (['penelitian', 'pkm'] as $jenis) {
            foreach (DkpsPenelitianPkmRingkasan::SUMBER_PEMBIAYAAN as $sumber) {
                DkpsPenelitianPkmRingkasan::create([
                    'dkps_submission_id' => $submission->id,
                    'jenis' => $jenis,
                    'sumber_pembiayaan' => $sumber,
                ]);
            }
        }

        $publikasiUrutan = 0;
        foreach (DkpsPublikasiDtps::MEDIA_PUBLIKASI as $media) {
            DkpsPublikasiDtps::create([
                'dkps_submission_id' => $submission->id,
                'media_publikasi' => $media,
                'urutan' => $publikasiUrutan++,
            ]);
        }
    }

    public function show(DkpsSubmission $dkp)
    {
        $dkp->load(['prodi', 'akreditasi', 'dosenTetap', 'tenagaKependidikan']);

        return view('admin.dkps.show', ['submission' => $dkp]);
    }

    public function edit(DkpsSubmission $dkp)
    {
        $prodis = $this->scopedProdiOptions();

        return view('admin.dkps.edit', ['submission' => $dkp, 'prodis' => $prodis]);
    }

    public function update(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'prodi_id' => 'required|exists:prodi,id',
            'akreditasi_id' => 'nullable|exists:akreditasi,id',
            'tahun_ts_awal' => 'required|integer|min:2000|max:2100',
            'tahun_ts_akhir' => 'required|integer|min:2000|max:2100',
            'nama_pengusul' => 'nullable|string|max:255',
            'tanggal_pengusulan' => 'nullable|date',
            'status' => 'required|in:draft,final',
        ]);

        $user = auth()->user();
        if (!$user->isAdmin() && $user->isKaprodi()) {
            abort_unless(
                $user->prodiDikepalai()->where('id', $validated['prodi_id'])->exists(),
                403,
                'Anda hanya dapat mengalihkan draft DKPS ke program studi Anda sendiri.'
            );
        }

        $dkp->update($validated);

        return redirect()->route('admin.dkps.show', $dkp)
            ->with('success', 'Draft DKPS berhasil diperbarui.');
    }

    public function destroy(DkpsSubmission $dkp)
    {
        $dkp->delete();

        return redirect()->route('admin.dkps.index')
            ->with('success', 'Draft DKPS berhasil dihapus.');
    }
}
