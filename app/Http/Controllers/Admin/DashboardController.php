<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akreditasi;
use App\Models\Auditor;
use App\Models\Berita;
use App\Models\Dokumen;
use App\Models\Galeri;
use App\Models\Kontak;
use App\Models\TemuanAmi;
use App\Models\TindakLanjut;
use App\Models\Visitor;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $scopedToKaprodi = !$isAdmin && $user->isKaprodi();
        $myProdi = $scopedToKaprodi ? $user->prodiDikepalai()->first() : null;

        $stats = [];
        $visitorStats = collect();
        $latestBerita = collect();
        $latestKontak = collect();

        // Site-content stats/activity are only meaningful to admins who manage
        // that content - auditor/kaprodi/viewer only care about the AMI panel below.
        if ($isAdmin) {
            $stats = [
                'berita' => Berita::count(),
                'dokumen' => Dokumen::count(),
                'galeri' => Galeri::count(),
                'users' => User::count(),
                'kontak_unread' => Kontak::unread()->count(),
                'visitors_today' => Visitor::getTodayCount(),
                'visitors_month' => Visitor::getMonthCount(),
                'visitors_total' => Visitor::getTotalCount(),
            ];

            $visitorStats = Visitor::select(
                    DB::raw('DATE(visit_date) as date'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('visit_date', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $latestBerita = Berita::latest()->take(5)->get();
            $latestKontak = Kontak::latest()->take(5)->get();
        }

        // AMI/Akreditasi "needs attention" panel - visible to every role that
        // can reach the dashboard (admin/auditor/kaprodi/viewer). For a kaprodi
        // this is scoped down to only the prodi they head; auditor certification
        // isn't prodi-tied, so it stays unscoped for everyone.
        $amiStats = [
            'akreditasi_expiring' => Akreditasi::expiringSoon()->when($scopedToKaprodi, fn ($q) => $q->ownedByKaprodi($user))->count(),
            'temuan_overdue' => TemuanAmi::overdue()->when($scopedToKaprodi, fn ($q) => $q->ownedByKaprodi($user))->count(),
            'tindak_lanjut_pending' => TindakLanjut::pendingReview()->when($scopedToKaprodi, fn ($q) => $q->ownedByKaprodi($user))->count(),
            'auditor_cert_expiring' => Auditor::certExpiringSoon()->count(),
        ];

        $akreditasiExpiringList = Akreditasi::expiringSoon()
            ->with('prodi')
            ->when($scopedToKaprodi, fn ($q) => $q->ownedByKaprodi($user))
            ->orderBy('tanggal_kadaluarsa')
            ->take(5)
            ->get();

        $temuanOverdueList = TemuanAmi::overdue()
            ->with('jadwalAmi.prodi')
            ->when($scopedToKaprodi, fn ($q) => $q->ownedByKaprodi($user))
            ->orderBy('batas_tindak_lanjut')
            ->take(5)
            ->get();

        $tindakLanjutPendingList = TindakLanjut::pendingReview()
            ->with(['temuanAmi.jadwalAmi.prodi', 'user'])
            ->when($scopedToKaprodi, fn ($q) => $q->ownedByKaprodi($user))
            ->latest()
            ->take(5)
            ->get();

        $auditorCertExpiringList = Auditor::certExpiringSoon()
            ->with('user')
            ->orderBy('masa_berlaku')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'visitorStats',
            'latestBerita',
            'latestKontak',
            'amiStats',
            'akreditasiExpiringList',
            'temuanOverdueList',
            'tindakLanjutPendingList',
            'auditorCertExpiringList',
            'myProdi'
        ));
    }
}
