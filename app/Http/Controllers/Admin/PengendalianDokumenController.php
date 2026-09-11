<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * Read-only control register across every Dokumen (all jenis, not just SPMI
 * policy documents) plus the ability to set a periodic review cycle per
 * document. Reuses the existing Dokumen/JenisDokumen module instead of a
 * separate document table.
 */
class PengendalianDokumenController extends Controller implements HasMiddleware
{
    private const OVERDUE_SQL = 'reviewed_at is not null and periode_tinjau_bulan is not null and date_add(reviewed_at, interval periode_tinjau_bulan month) < now()';

    public static function middleware(): array
    {
        return [
            new Middleware('permission:dokumen.view', only: ['index']),
            new Middleware('permission:dokumen.manage', only: ['updatePeriodeTinjau']),
        ];
    }

    public function index(Request $request)
    {
        $query = Dokumen::with('jenisDokumen')
            ->when($request->filled('jenis'), fn ($q) => $q->where('jenis_dokumen_id', $request->jenis))
            ->when($request->status === 'overdue', fn ($q) => $q->whereRaw(self::OVERDUE_SQL))
            ->when($request->status && $request->status !== 'overdue', fn ($q) => $q->where('status', $request->status))
            ->orderBy('judul');

        $dokumens = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Dokumen::count(),
            'approved' => Dokumen::where('status', 'approved')->count(),
            'ada_periode_tinjau' => Dokumen::whereNotNull('periode_tinjau_bulan')->count(),
            'overdue' => Dokumen::whereRaw(self::OVERDUE_SQL)->count(),
        ];

        $jenisDokumen = JenisDokumen::active()->ordered()->get();

        return view('admin.ami.pengendalian-dokumen.index', compact('dokumens', 'stats', 'jenisDokumen'));
    }

    public function updatePeriodeTinjau(Request $request, Dokumen $dokumen)
    {
        $validated = $request->validate([
            'periode_tinjau_bulan' => 'nullable|integer|min:1|max:120',
        ]);

        $dokumen->update($validated);

        return back()->with('success', 'Periode tinjau dokumen diperbarui.');
    }
}
