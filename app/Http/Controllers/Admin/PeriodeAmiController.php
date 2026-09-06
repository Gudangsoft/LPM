<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeAmi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PeriodeAmiController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:periode-ami.view', only: ['index', 'show']),
            new Middleware('permission:periode-ami.manage', only: ['create', 'store', 'edit', 'update', 'destroy', 'activate', 'complete']),
        ];
    }

    public function index(Request $request)
    {
        $query = PeriodeAmi::withCount('jadwalAmi')->latest();
        
        if ($request->has('tahun') && $request->tahun) {
            $query->where('tahun_akademik', $request->tahun);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $periodes = $query->paginate(15);
        $statusOptions = ['draft', 'aktif', 'selesai'];
        
        return view('admin.ami.periode.index', compact('periodes', 'statusOptions'));
    }

    public function create()
    {
        $semesterOptions = ['Ganjil', 'Genap'];
        
        return view('admin.ami.periode.create', compact('semesterOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tahun_akademik' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:draft,aktif,selesai',
            'deskripsi' => 'nullable|string',
        ]);

        PeriodeAmi::create($validated);

        return redirect()->route('admin.ami.periode.index')
            ->with('success', 'Periode AMI berhasil ditambahkan.');
    }

    public function show(PeriodeAmi $periode)
    {
        $periode->load(['jadwalAmi.prodi', 'jadwalAmi.penugasan.auditor.user']);
        
        return view('admin.ami.periode.show', compact('periode'));
    }

    public function edit(PeriodeAmi $periode)
    {
        $semesterOptions = ['Ganjil', 'Genap'];
        
        return view('admin.ami.periode.edit', compact('periode', 'semesterOptions'));
    }

    public function update(Request $request, PeriodeAmi $periode)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tahun_akademik' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:draft,aktif,selesai',
            'deskripsi' => 'nullable|string',
        ]);

        $periode->update($validated);

        return redirect()->route('admin.ami.periode.index')
            ->with('success', 'Periode AMI berhasil diperbarui.');
    }

    public function destroy(PeriodeAmi $periode)
    {
        if ($periode->jadwalAmi()->count() > 0) {
            return redirect()->route('admin.ami.periode.index')
                ->with('error', 'Periode tidak dapat dihapus karena masih memiliki jadwal AMI.');
        }
        
        $periode->delete();

        return redirect()->route('admin.ami.periode.index')
            ->with('success', 'Periode AMI berhasil dihapus.');
    }

    public function activate(PeriodeAmi $periode)
    {
        // Deactivate all other active periods
        PeriodeAmi::where('status', 'aktif')->update(['status' => 'draft']);
        
        $periode->update(['status' => 'aktif']);

        return redirect()->route('admin.ami.periode.show', $periode)
            ->with('success', 'Periode AMI berhasil diaktifkan.');
    }

    public function complete(PeriodeAmi $periode)
    {
        $periode->update(['status' => 'selesai']);

        return redirect()->route('admin.ami.periode.show', $periode)
            ->with('success', 'Periode AMI berhasil ditandai selesai.');
    }
}
