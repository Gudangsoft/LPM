<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function view($slug)
    {
        $dokumen = Dokumen::where('slug', $slug)->published()->firstOrFail();

        $disk = Storage::disk('public');

        if (blank($dokumen->file_path) || ! $disk->exists($dokumen->file_path)) {
            abort(404, __('messages.file_not_found'));
        }

        // Serve the file inline so the browser previews it instead of downloading.
        return $disk->response($dokumen->file_path, $dokumen->file_name, [
            'Content-Type' => $disk->mimeType($dokumen->file_path) ?: 'application/octet-stream',
        ]);
    }

    public function download($slug)
    {
        $dokumen = Dokumen::where('slug', $slug)->published()->firstOrFail();

        $disk = Storage::disk('public');

        if (blank($dokumen->file_path) || ! $disk->exists($dokumen->file_path)) {
            abort(404, __('messages.file_not_found'));
        }

        $dokumen->incrementDownload();

        $downloadName = $dokumen->file_name
            ?: Str::slug($dokumen->judul) . '.' . (pathinfo($dokumen->file_path, PATHINFO_EXTENSION) ?: 'pdf');

        return $disk->download($dokumen->file_path, $downloadName);
    }
}
