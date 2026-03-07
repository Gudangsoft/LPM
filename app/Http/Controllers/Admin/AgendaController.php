<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $query = Agenda::latest();
        
        if ($request->has('search') && $request->search) {
            $query->where('judul', 'like', "%{$request->search}%");
        }
        
        $agendas = $query->paginate(15);
        
        return view('admin.agenda.index', compact('agendas'));
    }

    public function create()
    {
        return view('admin.agenda.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        // Parse datetime-local format
        if ($request->tanggal_mulai) {
            $start = \Carbon\Carbon::parse($request->tanggal_mulai);
            $validated['tanggal_mulai'] = $start->format('Y-m-d');
            $validated['waktu_mulai'] = $start->format('H:i');
        }
        
        if ($request->tanggal_selesai) {
            $end = \Carbon\Carbon::parse($request->tanggal_selesai);
            $validated['tanggal_selesai'] = $end->format('Y-m-d');
            $validated['waktu_selesai'] = $end->format('H:i');
        }

        $validated['slug'] = Str::slug($validated['judul']);
        
        $count = 1;
        $originalSlug = $validated['slug'];
        while (Agenda::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        Agenda::create($validated);

        return redirect()->route('admin.agenda.index')
            ->with('success', __('admin.agenda_created'));
    }

    public function edit(Agenda $agenda)
    {
        return view('admin.agenda.edit', compact('agenda'));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        // Parse datetime-local format
        if ($request->tanggal_mulai) {
            $start = \Carbon\Carbon::parse($request->tanggal_mulai);
            $validated['tanggal_mulai'] = $start->format('Y-m-d');
            $validated['waktu_mulai'] = $start->format('H:i');
        }
        
        if ($request->tanggal_selesai) {
            $end = \Carbon\Carbon::parse($request->tanggal_selesai);
            $validated['tanggal_selesai'] = $end->format('Y-m-d');
            $validated['waktu_selesai'] = $end->format('H:i');
        }

        if ($agenda->judul !== $validated['judul']) {
            $validated['slug'] = Str::slug($validated['judul']);
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Agenda::where('slug', $validated['slug'])->where('id', '!=', $agenda->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        $agenda->update($validated);

        return redirect()->route('admin.agenda.index')
            ->with('success', __('admin.agenda_updated'));
    }

    public function destroy(Agenda $agenda)
    {
        $agenda->delete();

        return redirect()->route('admin.agenda.index')
            ->with('success', __('admin.agenda_deleted'));
    }
}
