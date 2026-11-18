<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Slider;
use App\Models\Agenda;
use App\Models\Pengumuman;
use App\Models\Galeri;
use App\Models\Halaman;

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

        return view('frontend.home', compact(
            'sliders',
            'berita',
            'pengumuman',
            'agenda',
            'galeri',
            'sambutanKetua'
        ));
    }
}
