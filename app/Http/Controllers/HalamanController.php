<?php

namespace App\Http\Controllers;

use App\Models\Halaman;
use App\Models\Prodi;
use App\Models\StrukturOrganisasi;
use Illuminate\Http\Request;

class HalamanController extends Controller
{
    public function profil()
    {
        $halaman = Halaman::where('slug', 'profil-lpm')->active()->first();
        return view('frontend.halaman.profil', compact('halaman'));
    }

    public function visiMisi()
    {
        $halaman = Halaman::where('slug', 'visi-misi')->active()->first();
        return view('frontend.halaman.visi-misi', compact('halaman'));
    }

    public function strukturOrganisasi()
    {
        $halaman = Halaman::where('slug', 'struktur-organisasi')->active()->first();
        $struktur = StrukturOrganisasi::active()->ordered()->get();
        return view('frontend.halaman.struktur-organisasi', compact('halaman', 'struktur'));
    }

    public function sistemPenjaminanMutu()
    {
        $halaman = Halaman::where('slug', 'sistem-penjaminan-mutu')->active()->first();
        return view('frontend.halaman.sistem-penjaminan-mutu', compact('halaman'));
    }

    public function auditMutuInternal()
    {
        $halaman = Halaman::where('slug', 'audit-mutu-internal')->active()->first();
        return view('frontend.halaman.audit-mutu-internal', compact('halaman'));
    }

    public function akreditasi()
    {
        $halaman = Halaman::where('slug', 'akreditasi')->active()->first();
        $prodis = Prodi::active()->with('activeAkreditasi')->orderBy('nama')->get();
        return view('frontend.halaman.akreditasi', compact('halaman', 'prodis'));
    }

    public function show($slug)
    {
        $halaman = Halaman::where('slug', $slug)->active()->firstOrFail();
        return view('frontend.halaman.show', compact('halaman'));
    }
}
