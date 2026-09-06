<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DkpsPenggunaanDana;
use App\Models\DkpsPrasarana;
use App\Models\DkpsSaranaLab;
use App\Models\DkpsSubmission;
use App\Models\DkpsTik;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DkpsSaranaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:dkps.manage'),
        ];
    }

    // ---- Penggunaan Dana (Table 12, fixed rows) ----

    public function updatePenggunaanDana(Request $request, DkpsSubmission $dkp)
    {
        $validated = $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:dkps_penggunaan_dana,id',
            'rows.*.jenis_penggunaan' => 'nullable|string|max:255',
            'rows.*.up_ps_ts2' => 'nullable|numeric|min:0',
            'rows.*.up_ps_ts1' => 'nullable|numeric|min:0',
            'rows.*.up_ps_ts' => 'nullable|numeric|min:0',
            'rows.*.ps_ts2' => 'nullable|numeric|min:0',
            'rows.*.ps_ts1' => 'nullable|numeric|min:0',
            'rows.*.ps_ts' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated['rows'] as $row) {
            DkpsPenggunaanDana::where('id', $row['id'])
                ->where('dkps_submission_id', $dkp->id)
                ->update(collect($row)->except('id')->toArray());
        }

        return back()->with('success', 'Data penggunaan dana berhasil disimpan.');
    }

    // ---- Sarana Lab (Table 13) ----

    public function storeSaranaLab(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateSaranaLab($request);
        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DkpsSaranaLab::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        DkpsSaranaLab::create($validated);

        return back()->with('success', 'Data sarana laboratorium berhasil ditambahkan.');
    }

    public function updateSaranaLab(Request $request, DkpsSubmission $dkp, DkpsSaranaLab $sarana)
    {
        $sarana->update($this->validateSaranaLab($request));

        return back()->with('success', 'Data sarana laboratorium berhasil diperbarui.');
    }

    public function destroySaranaLab(DkpsSubmission $dkp, DkpsSaranaLab $sarana)
    {
        $sarana->delete();

        return back()->with('success', 'Data sarana laboratorium berhasil dihapus.');
    }

    private function validateSaranaLab(Request $request): array
    {
        return $request->validate([
            'nama_lab_ruang' => 'required|string|max:255',
            'nama_alat_peraga' => 'required|string|max:255',
            'kualitas' => 'required|in:sangat_baik,baik,kurang_baik,tidak_baik',
            'jumlah' => 'nullable|integer|min:0',
            'kepemilikan' => 'required|in:milik_sendiri,sewa',
            'kondisi' => 'required|in:terawat,tidak_terawat',
            'rata_rata_jam_minggu' => 'nullable|numeric|min:0',
        ]);
    }

    // ---- Prasarana (Table 14) ----

    public function storePrasarana(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validatePrasarana($request);
        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DkpsPrasarana::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        DkpsPrasarana::create($validated);

        return back()->with('success', 'Data prasarana berhasil ditambahkan.');
    }

    public function updatePrasarana(Request $request, DkpsSubmission $dkp, DkpsPrasarana $prasarana)
    {
        $prasarana->update($this->validatePrasarana($request));

        return back()->with('success', 'Data prasarana berhasil diperbarui.');
    }

    public function destroyPrasarana(DkpsSubmission $dkp, DkpsPrasarana $prasarana)
    {
        $prasarana->delete();

        return back()->with('success', 'Data prasarana berhasil dihapus.');
    }

    private function validatePrasarana(Request $request): array
    {
        return $request->validate([
            'nama_prasarana' => 'required|string|max:255',
            'fungsi' => 'nullable|string|max:255',
            'jumlah_unit' => 'nullable|integer|min:0',
            'total_luas_m2' => 'nullable|numeric|min:0',
            'kualitas' => 'required|in:sangat_baik,baik,kurang_baik,tidak_baik',
            'kepemilikan' => 'required|in:milik_sendiri,sewa',
            'kondisi' => 'required|in:terawat,tidak_terawat',
        ]);
    }

    // ---- TIK (Table 15) ----

    public function storeTik(Request $request, DkpsSubmission $dkp)
    {
        $validated = $this->validateTik($request);
        $validated['dkps_submission_id'] = $dkp->id;
        $validated['urutan'] = DkpsTik::where('dkps_submission_id', $dkp->id)->max('urutan') + 1;

        DkpsTik::create($validated);

        return back()->with('success', 'Data TIK berhasil ditambahkan.');
    }

    public function updateTik(Request $request, DkpsSubmission $dkp, DkpsTik $tik)
    {
        $tik->update($this->validateTik($request));

        return back()->with('success', 'Data TIK berhasil diperbarui.');
    }

    public function destroyTik(DkpsSubmission $dkp, DkpsTik $tik)
    {
        $tik->delete();

        return back()->with('success', 'Data TIK berhasil dihapus.');
    }

    private function validateTik(Request $request): array
    {
        $validated = $request->validate([
            'nama_infrastruktur' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:255',
            'jumlah' => 'nullable|integer|min:0',
            'terintegrasi' => 'required|in:penuh,sebagian,tidak_terintegrasi',
            'mutahir' => 'required|in:mutahir,tidak_mutahir',
            'ada_panduan' => 'nullable|boolean',
            'kepemilikan' => 'required|in:milik_sendiri,sewa',
            'kondisi' => 'required|in:terawat,tidak_terawat',
        ]);

        $validated['ada_panduan'] = $request->boolean('ada_panduan');

        return $validated;
    }
}
