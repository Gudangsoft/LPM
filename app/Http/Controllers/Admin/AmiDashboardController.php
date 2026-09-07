<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\JadwalAmi;
use App\Models\PeriodeAmi;
use App\Models\Prodi;
use App\Models\TemuanAmi;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class AmiDashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:periode-ami.view', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $scoped = ! $user->isAdmin() && $user->isKaprodi();

        $periodes = PeriodeAmi::orderByDesc('tanggal_mulai')->get();
        $periode = $request->filled('periode_id')
            ? $periodes->firstWhere('id', (int) $request->periode_id)
            : ($periodes->firstWhere('status', 'aktif') ?? $periodes->first());

        $periodeId = $periode?->id;

        // Base builders scoped to the chosen periode (+ kaprodi's own prodi).
        $jadwal = JadwalAmi::query()
            ->when($periodeId, fn ($q) => $q->where('periode_ami_id', $periodeId))
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user));

        $temuan = TemuanAmi::query()
            ->when($periodeId, fn ($q) => $q->whereHas('jadwalAmi', fn ($j) => $j->where('periode_ami_id', $periodeId)))
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user));

        $rtl = TindakLanjut::query()
            ->when($periodeId, fn ($q) => $q->whereHas('temuanAmi.jadwalAmi', fn ($j) => $j->where('periode_ami_id', $periodeId)))
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user));

        $temuanSelesai = (clone $temuan)->whereIn('status', ['closed', 'verified'])->count();
        $temuanBelum = (clone $temuan)->whereIn('status', ['open', 'in_progress'])->count();
        $temuanTotal = $temuanSelesai + $temuanBelum;
        $rtlTotal = (clone $rtl)->count();
        $rtlApproved = (clone $rtl)->where('status', 'approved')->count();

        $cards = [
            'periode' => $periode?->nama ?? '—',
            'unit_diaudit' => (clone $jadwal)->distinct('prodi_id')->count('prodi_id'),
            'auditor' => Auditor::where('status', 'aktif')->count(),
            'temuan_total' => $temuanTotal,
            'temuan_selesai' => $temuanSelesai,
            'temuan_belum' => $temuanBelum,
            'rtl_persen' => $rtlTotal > 0 ? round($rtlApproved / $rtlTotal * 100, 1) : 0,
        ];

        // ---- charts -------------------------------------------------------
        $jadwalStatus = (clone $jadwal)->select('status', DB::raw('count(*) as n'))
            ->groupBy('status')->pluck('n', 'status');

        $temuanStatus = (clone $temuan)->select('status', DB::raw('count(*) as n'))
            ->groupBy('status')->pluck('n', 'status');

        $temuanKategori = (clone $temuan)->select('kategori', DB::raw('count(*) as n'))
            ->groupBy('kategori')->pluck('n', 'kategori');

        $temuanPerUnit = (clone $temuan)
            ->join('jadwal_ami', 'temuan_ami.jadwal_ami_id', '=', 'jadwal_ami.id')
            ->join('prodi', 'jadwal_ami.prodi_id', '=', 'prodi.id')
            ->select('prodi.nama', DB::raw('count(*) as n'))
            ->groupBy('prodi.id', 'prodi.nama')
            ->orderByDesc('n')->limit(10)->pluck('n', 'nama');

        $temuanPerStandar = (clone $temuan)
            ->leftJoin('standar_mutu', 'temuan_ami.standar_mutu_id', '=', 'standar_mutu.id')
            ->select(DB::raw('COALESCE(standar_mutu.nama, temuan_ami.standar) as label'), DB::raw('count(*) as n'))
            ->groupBy('label')
            ->orderByDesc('n')->limit(12)->pluck('n', 'label');

        $chart = [
            'jadwalStatus' => $jadwalStatus,
            'temuanStatus' => $temuanStatus,
            'temuanKategori' => $temuanKategori,
            'temuanPerUnit' => $temuanPerUnit,
            'temuanPerStandar' => $temuanPerStandar,
        ];

        return view('admin.ami.dashboard', compact('cards', 'chart', 'periodes', 'periode'));
    }
}
