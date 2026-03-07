<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\KategoriBerita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::published()->latest();
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('konten', 'like', "%{$search}%")
                  ->orWhere('ringkasan', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('kategori') && $request->kategori) {
            $query->whereHas('kategori', function($q) use ($request) {
                $q->where('slug', $request->kategori);
            });
        }
        
        $berita = $query->paginate(12);
        $kategoris = KategoriBerita::active()->withCount('berita')->get();
        
        return view('frontend.berita.index', compact('berita', 'kategoris'));
    }

    public function show($slug)
    {
        $berita = Berita::where('slug', $slug)->published()->firstOrFail();
        $berita->incrementViews();
        
        $relatedBerita = Berita::published()
            ->where('id', '!=', $berita->id)
            ->where('kategori_id', $berita->kategori_id)
            ->latest()
            ->take(4)
            ->get();
        
        return view('frontend.berita.show', compact('berita', 'relatedBerita'));
    }

    public function kategori($slug)
    {
        $kategori = KategoriBerita::where('slug', $slug)->firstOrFail();
        $berita = Berita::published()
            ->where('kategori_id', $kategori->id)
            ->latest()
            ->paginate(12);
        
        return view('frontend.berita.kategori', compact('kategori', 'berita'));
    }
}
