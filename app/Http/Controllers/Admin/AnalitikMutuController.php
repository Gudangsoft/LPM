<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeAmi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class AnalitikMutuController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:laporan-ami.view', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $kaprodiId = ! $user->isAdmin() && $user->isKaprodi() ? $user->id : null;

        $labelExpr = 'COALESCE(standar_mutu.nama, temuan_ami.standar)';

        // Tren temuan per periode
        $temuanPerPeriode = DB::table('temuan_ami')
            ->join('jadwal_ami', 'temuan_ami.jadwal_ami_id', '=', 'jadwal_ami.id')
            ->join('periode_ami', 'jadwal_ami.periode_ami_id', '=', 'periode_ami.id')
            ->join('prodi', 'jadwal_ami.prodi_id', '=', 'prodi.id')
            ->when($kaprodiId, fn ($q) => $q->where('prodi.kaprodi_id', $kaprodiId))
            ->groupBy('periode_ami.id', 'periode_ami.nama', 'periode_ami.tanggal_mulai')
            ->orderBy('periode_ami.tanggal_mulai')
            ->pluck(DB::raw('count(*)'), 'periode_ami.nama');

        // Tren rata-rata nilai auditor per periode
        $nilaiPerPeriode = DB::table('audit_butir')
            ->join('jadwal_ami', 'audit_butir.jadwal_ami_id', '=', 'jadwal_ami.id')
            ->join('periode_ami', 'jadwal_ami.periode_ami_id', '=', 'periode_ami.id')
            ->join('prodi', 'jadwal_ami.prodi_id', '=', 'prodi.id')
            ->whereNotNull('audit_butir.nilai_auditor')
            ->when($kaprodiId, fn ($q) => $q->where('prodi.kaprodi_id', $kaprodiId))
            ->groupBy('periode_ami.id', 'periode_ami.nama', 'periode_ami.tanggal_mulai')
            ->orderBy('periode_ami.tanggal_mulai')
            ->pluck(DB::raw('ROUND(AVG(audit_butir.nilai_auditor), 2)'), 'periode_ami.nama');

        // Temuan berulang: (unit, standar) di lebih dari 1 periode
        $temuanBerulang = DB::table('temuan_ami')
            ->join('jadwal_ami', 'temuan_ami.jadwal_ami_id', '=', 'jadwal_ami.id')
            ->join('prodi', 'jadwal_ami.prodi_id', '=', 'prodi.id')
            ->leftJoin('standar_mutu', 'temuan_ami.standar_mutu_id', '=', 'standar_mutu.id')
            ->when($kaprodiId, fn ($q) => $q->where('prodi.kaprodi_id', $kaprodiId))
            ->groupBy('prodi.id', 'prodi.nama', DB::raw($labelExpr))
            ->havingRaw('COUNT(DISTINCT jadwal_ami.periode_ami_id) > 1')
            ->select(
                'prodi.nama as unit',
                DB::raw("$labelExpr as label"),
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(DISTINCT jadwal_ami.periode_ami_id) as periode')
            )
            ->orderByDesc('total')->limit(30)->get();

        // Perbandingan antar unit - dua query terpisah agar tidak saling menggandakan
        $temuanUnit = DB::table('temuan_ami')
            ->join('jadwal_ami', 'temuan_ami.jadwal_ami_id', '=', 'jadwal_ami.id')
            ->join('prodi', 'jadwal_ami.prodi_id', '=', 'prodi.id')
            ->when($kaprodiId, fn ($q) => $q->where('prodi.kaprodi_id', $kaprodiId))
            ->groupBy('prodi.id', 'prodi.nama')
            ->select(
                'prodi.nama as unit',
                DB::raw('COUNT(*) as temuan'),
                DB::raw("SUM(CASE WHEN temuan_ami.status IN ('closed','verified') THEN 1 ELSE 0 END) as selesai")
            )->get()->keyBy('unit');

        $nilaiUnit = DB::table('audit_butir')
            ->join('jadwal_ami', 'audit_butir.jadwal_ami_id', '=', 'jadwal_ami.id')
            ->join('prodi', 'jadwal_ami.prodi_id', '=', 'prodi.id')
            ->whereNotNull('audit_butir.nilai_auditor')
            ->when($kaprodiId, fn ($q) => $q->where('prodi.kaprodi_id', $kaprodiId))
            ->groupBy('prodi.id', 'prodi.nama')
            ->pluck(DB::raw('ROUND(AVG(audit_butir.nilai_auditor), 2)'), 'prodi.nama');

        $perUnit = $temuanUnit->map(fn ($r) => (object) [
            'unit' => $r->unit,
            'temuan' => (int) $r->temuan,
            'selesai' => (int) $r->selesai,
            'persen' => $r->temuan > 0 ? round($r->selesai / $r->temuan * 100) : 0,
            'rata_nilai' => $nilaiUnit[$r->unit] ?? null,
        ])->values();

        $periodes = PeriodeAmi::orderByDesc('tanggal_mulai')->get();

        return view('admin.ami.analitik.index', compact(
            'temuanPerPeriode', 'nilaiPerPeriode', 'temuanBerulang', 'perUnit', 'periodes'
        ));
    }
}
