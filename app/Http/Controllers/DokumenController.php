<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dokumen::published()->latest();
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        
        $dokumen = $query->paginate(20);
        $kategoris = Dokumen::published()
            ->distinct()
            ->whereNotNull('kategori')
            ->pluck('kategori');

        return view('frontend.dokumen.index', compact('dokumen', 'kategoris'));
    }

    public function download($slug)
    {
        $dokumen = Dokumen::where('slug', $slug)->published()->firstOrFail();
        $dokumen->incrementDownload();
        
        return Storage::disk('public')->download($dokumen->file_path, $dokumen->file_name);
    }
}
