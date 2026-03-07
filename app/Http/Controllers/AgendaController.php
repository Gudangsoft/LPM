<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index()
    {
        $upcoming = Agenda::active()->upcoming()->orderBy('tanggal_mulai')->paginate(10);
        
        return view('frontend.agenda.index', compact('upcoming'));
    }

    public function show($slug)
    {
        $agenda = Agenda::where('slug', $slug)->active()->firstOrFail();
        
        return view('frontend.agenda.show', compact('agenda'));
    }
}
