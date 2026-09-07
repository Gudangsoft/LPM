<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuktiAudit;
use App\Models\PeriodeAmi;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BuktiAuditController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:bukti.view', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $scoped = ! $user->isAdmin() && $user->isKaprodi();

        $query = BuktiAudit::query()
            ->with(['uploader', 'auditButir.butir.standarMutu', 'auditButir.jadwalAmi.prodi', 'auditButir.jadwalAmi.periodeAmi'])
            ->join('audit_butir', 'bukti_audit.audit_butir_id', '=', 'audit_butir.id')
            ->join('jadwal_ami', 'audit_butir.jadwal_ami_id', '=', 'jadwal_ami.id')
            ->select('bukti_audit.*')
            ->latest('bukti_audit.id');

        if ($request->filled('periode_id')) {
            $query->where('jadwal_ami.periode_ami_id', $request->periode_id);
        }
        if ($request->filled('prodi_id')) {
            $query->where('jadwal_ami.prodi_id', $request->prodi_id);
        }
        if ($request->filled('status')) {
            $query->where('bukti_audit.status_validasi', $request->status);
        }
        if ($scoped) {
            $query->whereHas('auditButir.jadwalAmi.prodi', fn ($q) => $q->where('kaprodi_id', $user->id));
        }

        $bukti = $query->paginate(20)->withQueryString();
        $periodes = PeriodeAmi::orderByDesc('tanggal_mulai')->get();
        $prodis = Prodi::when($scoped, fn ($q) => $q->where('kaprodi_id', $user->id))->orderBy('nama')->get();

        return view('admin.ami.bukti.index', compact('bukti', 'periodes', 'prodis'));
    }
}
