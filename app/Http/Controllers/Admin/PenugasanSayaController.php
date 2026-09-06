<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\PenugasanAmi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PenugasanSayaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:penugasan.respond'),
        ];
    }

    public function index()
    {
        $auditor = Auditor::where('user_id', auth()->id())->first();

        if (!$auditor) {
            return view('admin.ami.penugasan.saya', ['penugasans' => null]);
        }

        $penugasans = $auditor->penugasan()
            ->with(['jadwalAmi.prodi', 'jadwalAmi.periodeAmi'])
            ->latest()
            ->paginate(15);

        return view('admin.ami.penugasan.saya', compact('penugasans'));
    }

    public function accept(PenugasanAmi $penugasan)
    {
        abort_unless($penugasan->auditor->user_id === auth()->id(), 403);

        $penugasan->accept();

        return back()->with('success', 'Penugasan berhasil diterima.');
    }

    public function reject(Request $request, PenugasanAmi $penugasan)
    {
        abort_unless($penugasan->auditor->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        $penugasan->reject($validated['catatan'] ?? null);

        return back()->with('success', 'Penugasan berhasil ditolak.');
    }
}
