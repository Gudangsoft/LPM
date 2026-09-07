<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\TemuanAmi;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * Ruang kerja verifikasi RTL: auditor menilai tindak lanjut yang diajukan
 * auditee (terima -> temuan ditutup, atau revisi -> auditee memperbaiki).
 */
class VerifikasiRtlController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:tindak-lanjut.view', only: ['index']),
            new Middleware('permission:tindak-lanjut.review', only: ['verify']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $base = TindakLanjut::with(['temuanAmi.jadwalAmi.prodi', 'temuanAmi.standarMutu', 'user', 'reviewer'])
            ->when(! $user->isAdmin() && $user->isAuditor(),
                fn ($q) => $q->whereHas('temuanAmi.auditor', fn ($a) => $a->where('user_id', $user->id)))
            ->when(! $user->isAdmin() && $user->isKaprodi(),
                fn ($q) => $q->ownedByKaprodi($user))
            ->when($request->filled('prodi_id'),
                fn ($q) => $q->whereHas('temuanAmi.jadwalAmi', fn ($j) => $j->where('prodi_id', $request->prodi_id)));

        $menunggu = (clone $base)->where('status', 'submitted')
            ->latest()->paginate(15)->withQueryString();

        $riwayat = (clone $base)->whereIn('status', ['approved', 'rejected'])
            ->latest('reviewed_at')->limit(20)->get();

        $stats = [
            'menunggu' => (clone $base)->where('status', 'submitted')->count(),
            'disetujui' => (clone $base)->where('status', 'approved')->count(),
            'revisi' => (clone $base)->where('status', 'rejected')->count(),
        ];

        $prodis = Prodi::orderBy('nama')->get();

        return view('admin.ami.verifikasi-rtl.index', compact('menunggu', 'riwayat', 'stats', 'prodis'));
    }

    public function verify(Request $request, TindakLanjut $tindakLanjut)
    {
        abort_unless($tindakLanjut->status === 'submitted', 409, 'Tindak lanjut ini sudah diverifikasi.');

        $this->authorizeAuditor($tindakLanjut);

        $data = $request->validate([
            'aksi' => 'required|in:terima,revisi',
            'catatan_reviewer' => 'nullable|string',
        ]);

        if ($data['aksi'] === 'terima') {
            $tindakLanjut->approve(auth()->user(), $data['catatan_reviewer'] ?? null);
            $msg = 'RTL diterima. Temuan ditutup.';
        } else {
            if (blank($data['catatan_reviewer'] ?? null)) {
                return back()->with('error', 'Catatan wajib diisi saat meminta revisi.');
            }
            $tindakLanjut->reject(auth()->user(), $data['catatan_reviewer']);
            $msg = 'RTL dikembalikan untuk revisi.';
        }

        return back()->with('success', $msg);
    }

    private function authorizeAuditor(TindakLanjut $tindakLanjut): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        $isAuditor = $tindakLanjut->temuanAmi
            ?->auditor
            ?->user_id === $user->id;

        abort_unless($isAuditor, 403, 'Hanya auditor pemilik temuan yang dapat memverifikasi RTL ini.');
    }
}
