<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalAmi;
use App\Models\PeriodeAmi;
use App\Models\Prodi;
use App\Models\Auditor;
use App\Models\PenugasanAmi;
use Illuminate\Http\Request;

class JadwalAmiController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalAmi::with(['periodeAmi', 'prodi', 'penugasan.auditor.user'])
            ->latest('tanggal_audit');
        
        if ($request->has('periode_id') && $request->periode_id) {
            $query->where('periode_ami_id', $request->periode_id);
        }
        
        if ($request->has('prodi_id') && $request->prodi_id) {
            $query->where('prodi_id', $request->prodi_id);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $jadwals = $query->paginate(15);
        $periodes = PeriodeAmi::orderByDesc('tanggal_mulai')->get();
        $prodis = Prodi::active()->orderBy('nama')->get();
        $statusOptions = ['terjadwal', 'berlangsung', 'selesai', 'ditunda', 'batal'];
        
        return view('admin.ami.jadwal.index', compact('jadwals', 'periodes', 'prodis', 'statusOptions'));
    }

    public function create()
    {
        $periodes = PeriodeAmi::where('status', '!=', 'selesai')->orderByDesc('tanggal_mulai')->get();
        $prodis = Prodi::active()->orderBy('nama')->get();
        
        return view('admin.ami.jadwal.create', compact('periodes', 'prodis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode_ami_id' => 'required|exists:periode_ami,id',
            'prodi_id' => 'required|exists:prodi,id',
            'tanggal_audit' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'tempat' => 'nullable|string|max:255',
            'status' => 'required|in:terjadwal,berlangsung,selesai,ditunda,batal',
            'catatan' => 'nullable|string',
        ]);

        $jadwal = JadwalAmi::create($validated);

        return redirect()->route('admin.ami.jadwal.show', $jadwal)
            ->with('success', 'Jadwal AMI berhasil ditambahkan.');
    }

    public function show(JadwalAmi $jadwal)
    {
        $jadwal->load([
            'periodeAmi',
            'prodi.kaprodi',
            'penugasan.auditor.user',
            'temuan.auditor.user',
            'temuan.tindakLanjut',
        ]);
        
        $availableAuditors = Auditor::available()
            ->whereNotIn('id', $jadwal->penugasan()->pluck('auditor_id'))
            ->with('user')
            ->get();
        
        return view('admin.ami.jadwal.show', compact('jadwal', 'availableAuditors'));
    }

    public function edit(JadwalAmi $jadwal)
    {
        $periodes = PeriodeAmi::where('status', '!=', 'selesai')->orderByDesc('tanggal_mulai')->get();
        $prodis = Prodi::active()->orderBy('nama')->get();
        
        return view('admin.ami.jadwal.edit', compact('jadwal', 'periodes', 'prodis'));
    }

    public function update(Request $request, JadwalAmi $jadwal)
    {
        $validated = $request->validate([
            'periode_ami_id' => 'required|exists:periode_ami,id',
            'prodi_id' => 'required|exists:prodi,id',
            'tanggal_audit' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'tempat' => 'nullable|string|max:255',
            'status' => 'required|in:terjadwal,berlangsung,selesai,ditunda,batal',
            'catatan' => 'nullable|string',
        ]);

        $jadwal->update($validated);

        return redirect()->route('admin.ami.jadwal.show', $jadwal)
            ->with('success', 'Jadwal AMI berhasil diperbarui.');
    }

    public function destroy(JadwalAmi $jadwal)
    {
        if ($jadwal->temuan()->count() > 0) {
            return redirect()->route('admin.ami.jadwal.index')
                ->with('error', 'Jadwal tidak dapat dihapus karena sudah memiliki temuan.');
        }
        
        $jadwal->penugasan()->delete();
        $jadwal->delete();

        return redirect()->route('admin.ami.jadwal.index')
            ->with('success', 'Jadwal AMI berhasil dihapus.');
    }

    /**
     * Add auditor to jadwal
     */
    public function addAuditor(Request $request, JadwalAmi $jadwal)
    {
        $validated = $request->validate([
            'auditor_id' => 'required|exists:auditor,id',
            'peran' => 'required|in:ketua,anggota',
        ]);

        // Check if already assigned
        if ($jadwal->penugasan()->where('auditor_id', $validated['auditor_id'])->exists()) {
            return back()->with('error', 'Auditor sudah ditugaskan pada jadwal ini.');
        }

        // If assigning as ketua, demote existing ketua
        if ($validated['peran'] === 'ketua') {
            $jadwal->penugasan()->where('peran', 'ketua')->update(['peran' => 'anggota']);
        }

        PenugasanAmi::create([
            'jadwal_ami_id' => $jadwal->id,
            'auditor_id' => $validated['auditor_id'],
            'peran' => $validated['peran'],
            'status' => 'ditugaskan',
        ]);

        return back()->with('success', 'Auditor berhasil ditugaskan.');
    }

    /**
     * Remove auditor from jadwal
     */
    public function removeAuditor(JadwalAmi $jadwal, PenugasanAmi $penugasan)
    {
        if ($penugasan->jadwal_ami_id !== $jadwal->id) {
            abort(404);
        }
        
        $penugasan->delete();

        return back()->with('success', 'Penugasan auditor berhasil dihapus.');
    }

    /**
     * Update jadwal status
     */
    public function updateStatus(Request $request, JadwalAmi $jadwal)
    {
        $validated = $request->validate([
            'status' => 'required|in:terjadwal,berlangsung,selesai,ditunda,batal',
        ]);

        $jadwal->update($validated);

        // If marked as selesai, also complete all penugasan
        if ($validated['status'] === 'selesai') {
            $jadwal->penugasan()->where('status', 'diterima')->update(['status' => 'selesai']);
        }

        return back()->with('success', 'Status jadwal berhasil diperbarui.');
    }
}
