<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RtmKeputusan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * Cross-RTM view of every keputusan/rekomendasi (rtm_keputusan) as a single
 * quality-improvement action list, instead of having to open each Rapat
 * Tinjauan Manajemen individually. Status updates reuse the existing
 * RtmController::updateKeputusan action - no separate write endpoint here.
 */
class RencanaPeningkatanMutuController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:rtm.view', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        $query = RtmKeputusan::with('rtm.periodeAmi')
            ->when($request->filled('periode_ami_id'), fn ($q) => $q->whereHas('rtm', fn ($r) => $r->where('periode_ami_id', $request->periode_ami_id)))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('pic'), fn ($q) => $q->where('pic', 'like', "%{$request->pic}%"))
            ->when($request->filled('search'), fn ($q) => $q->where('keputusan', 'like', "%{$request->search}%"))
            ->orderByDesc('target_tanggal');

        $rencana = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => (clone $query)->count(),
            'selesai' => (clone $query)->where('status', 'selesai')->count(),
            'proses' => (clone $query)->where('status', 'proses')->count(),
            'belum' => (clone $query)->where('status', 'belum')->count(),
        ];

        $periodeOptions = \App\Models\PeriodeAmi::orderByDesc('tahun_akademik')->get(['id', 'nama', 'tahun_akademik']);

        return view('admin.ami.rencana-peningkatan-mutu.index', compact('rencana', 'stats', 'periodeOptions'));
    }
}
