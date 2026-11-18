<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Slider;
use App\Models\Agenda;
use App\Models\Pengumuman;
use App\Models\Galeri;
use App\Models\Halaman;
use App\Models\StrukturOrganisasi;
use App\Models\Pengaturan;
use App\Models\Akreditasi;
use App\Models\Auditor;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::active()->ordered()->get();
        $berita = Berita::published()->latest()->take(6)->get();
        $pengumuman = Pengumuman::active()->latest()->take(5)->get();
        $agenda = Agenda::active()->upcoming()->orderBy('tanggal_mulai')->take(5)->get();
        $galeri = Galeri::active()->ordered()->take(8)->get();
        
        $sambutanKetua = Halaman::where('slug', 'sambutan-ketua')->first();
        
        // Statistik slider: otomatis dari data, bisa ditimpa di Pengaturan > Template.
        $auto = static::autoStats();
        $manual = fn (string $key) => ctype_digit((string) Pengaturan::get($key, '')) ? (int) Pengaturan::get($key) : null;
        $stats = [
            'prodi' => $manual('hero_stat_prodi') ?? $auto['prodi'],
            'auditor' => $manual('hero_stat_auditor') ?? $auto['auditor'],
            'tahun' => $auto['tahun'],
        ];

        return view('frontend.home', compact(
            'sliders', 
            'berita', 
            'pengumuman', 
            'agenda', 
            'galeri',
            'sambutanKetua',
            'stats'
        ));
    }

    /**
     * Angka statistik otomatis dari data sistem.
     */
    public static function autoStats(): array
    {
        $berdiri = (int) Pengaturan::get('hero_tahun_berdiri', 2015) ?: 2015;

        return [
            // prodi yang punya akreditasi aktif dan belum kedaluwarsa
            'prodi' => Akreditasi::query()
                ->where('status', 'aktif')
                ->where(fn ($q) => $q->whereNull('tanggal_kadaluarsa')->orWhere('tanggal_kadaluarsa', '>=', now()->toDateString()))
                ->distinct()
                ->count('prodi_id'),
            'auditor' => Auditor::where('status', 'aktif')->count(),
            'tahun' => max(0, now()->year - $berdiri),
        ];
    }
}
