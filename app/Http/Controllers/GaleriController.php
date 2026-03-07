<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        $query = Galeri::active()->ordered();
        
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        
        $galeri = $query->paginate(16);
        $kategoris = Galeri::active()
            ->distinct()
            ->whereNotNull('kategori')
            ->pluck('kategori');
        
        return view('frontend.galeri.index', compact('galeri', 'kategoris'));
    }

    public function show($slug)
    {
        $galeri = Galeri::where('slug', $slug)->active()->firstOrFail();
        
        return view('frontend.galeri.show', compact('galeri'));
    }
}
