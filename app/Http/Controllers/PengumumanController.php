<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::active()->latest()->paginate(10);
        
        return view('frontend.pengumuman.index', compact('pengumuman'));
    }

    public function show($slug)
    {
        $pengumuman = Pengumuman::where('slug', $slug)->active()->firstOrFail();
        
        return view('frontend.pengumuman.show', compact('pengumuman'));
    }
}
