<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Dokumen;
use App\Models\Galeri;
use App\Models\Kontak;
use App\Models\Visitor;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
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

        // Visitor statistics for chart
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

        return view('admin.dashboard', compact('stats', 'visitorStats', 'latestBerita', 'latestKontak'));
    }
}
