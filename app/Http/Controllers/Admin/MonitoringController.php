<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeAmi;
use App\Models\Prodi;
use App\Models\TemuanAmi;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:tindak-lanjut.view', only: ['index']),
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

        $temuan = fn () => TemuanAmi::query()
            ->when($periodeId, fn ($q) => $q->whereHas('jadwalAmi', fn ($j) => $j->where('periode_ami_id', $periodeId)))
            ->when($request->filled('prodi_id'), fn ($q) => $q->whereHas('jadwalAmi', fn ($j) => $j->where('prodi_id', $request->prodi_id)))
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user));

        $rtl = fn () => TindakLanjut::query()
            ->when($periodeId, fn ($q) => $q->whereHas('temuanAmi.jadwalAmi', fn ($j) => $j->where('periode_ami_id', $periodeId)))
            ->when($request->filled('prodi_id'), fn ($q) => $q->whereHas('temuanAmi.jadwalAmi', fn ($j) => $j->where('prodi_id', $request->prodi_id)))
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user));

        $cards = [
            'open' => $temuan()->where('status', 'open')->count(),
            'in_progress' => $temuan()->where('status', 'in_progress')->count(),
            'overdue' => $temuan()->overdue()->count(),
            'closed' => $temuan()->whereIn('status', ['closed', 'verified'])->count(),
            'rtl_submitted' => $rtl()->where('status', 'submitted')->count(),
            'rtl_approved' => $rtl()->where('status', 'approved')->count(),
            'rtl_rejected' => $rtl()->where('status', 'rejected')->count(),
        ];

        // Per-prodi rollup
        $rollup = TemuanAmi::query()
            ->join('jadwal_ami', 'temuan_ami.jadwal_ami_id', '=', 'jadwal_ami.id')
            ->join('prodi', 'jadwal_ami.prodi_id', '=', 'prodi.id')
            ->when($periodeId, fn ($q) => $q->where('jadwal_ami.periode_ami_id', $periodeId))
            ->when($request->filled('prodi_id'), fn ($q) => $q->where('prodi.id', $request->prodi_id))
            ->when($scoped, fn ($q) => $q->where('prodi.kaprodi_id', $user->id))
            ->groupBy('prodi.id', 'prodi.nama')
            ->select(
                'prodi.nama',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN temuan_ami.status IN ('closed','verified') THEN 1 ELSE 0 END) as selesai"),
                DB::raw("SUM(CASE WHEN temuan_ami.status IN ('open','in_progress') THEN 1 ELSE 0 END) as belum"),
                DB::raw("SUM(CASE WHEN temuan_ami.status != 'closed' AND temuan_ami.status != 'verified' AND temuan_ami.batas_tindak_lanjut IS NOT NULL AND temuan_ami.batas_tindak_lanjut < CURDATE() THEN 1 ELSE 0 END) as telat")
            )
            ->orderByDesc('total')
            ->get();

        // Overdue findings list
        $overdueList = $temuan()->overdue()
            ->with(['jadwalAmi.prodi', 'standarMutu'])
            ->orderBy('batas_tindak_lanjut')
            ->paginate(15)
            ->withQueryString();

        $prodis = Prodi::when($scoped, fn ($q) => $q->where('kaprodi_id', $user->id))
            ->orderBy('nama')->get();

        return view('admin.ami.monitoring.index', compact(
            'cards', 'rollup', 'overdueList', 'periodes', 'periode', 'prodis'
        ));
    }
}
