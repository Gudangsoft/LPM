<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemuanAmi;
use App\Models\JadwalAmi;
use App\Models\Auditor;
use Illuminate\Http\Request;

class TemuanAmiController extends Controller
{
    public function index(Request $request)
    {
        $query = TemuanAmi::with(['jadwalAmi.prodi', 'auditor.user'])
            ->latest();
        
        if ($request->has('jadwal_id') && $request->jadwal_id) {
            $query->where('jadwal_ami_id', $request->jadwal_id);
        }
        
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $temuans = $query->paginate(15);
        $kategoriOptions = ['mayor', 'minor', 'observasi', 'rekomendasi'];
        $statusOptions = ['open', 'in_progress', 'closed', 'verified'];
        
        return view('admin.ami.temuan.index', compact('temuans', 'kategoriOptions', 'statusOptions'));
    }

    public function create(Request $request)
    {
        $jadwalId = $request->get('jadwal_id');
        $jadwal = $jadwalId ? JadwalAmi::with('prodi')->find($jadwalId) : null;
        
        $jadwals = JadwalAmi::with('prodi')
            ->whereIn('status', ['berlangsung', 'selesai'])
            ->orderByDesc('tanggal_audit')
            ->get();
        
        // Get auditors assigned to the selected jadwal or all active auditors
        $auditors = $jadwal 
            ? $jadwal->penugasan()->with('auditor.user')->get()->pluck('auditor')
            : Auditor::aktif()->with('user')->get();
        
        $kategoriOptions = ['mayor', 'minor', 'observasi', 'rekomendasi'];
        $standarOptions = [
            'Standar 1 - Visi, Misi, Tujuan dan Strategi',
            'Standar 2 - Tata Pamong, Tata Kelola dan Kerjasama',
            'Standar 3 - Mahasiswa',
            'Standar 4 - Sumber Daya Manusia',
            'Standar 5 - Keuangan, Sarana dan Prasarana',
            'Standar 6 - Pendidikan',
            'Standar 7 - Penelitian',
            'Standar 8 - Pengabdian kepada Masyarakat',
            'Standar 9 - Luaran dan Capaian Tridharma',
        ];
        
        return view('admin.ami.temuan.create', compact(
            'jadwal', 
            'jadwals', 
            'auditors', 
            'kategoriOptions', 
            'standarOptions'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_ami_id' => 'required|exists:jadwal_ami,id',
            'auditor_id' => 'required|exists:auditor,id',
            'standar' => 'required|string|max:255',
            'kategori' => 'required|in:mayor,minor,observasi,rekomendasi',
            'deskripsi' => 'required|string',
            'bukti' => 'nullable|string',
            'akar_masalah' => 'nullable|string',
            'rekomendasi' => 'required|string',
            'batas_tindak_lanjut' => 'nullable|date',
            'status' => 'required|in:open,in_progress,closed,verified',
        ]);

        $temuan = TemuanAmi::create($validated);

        return redirect()->route('admin.ami.temuan.show', $temuan)
            ->with('success', 'Temuan berhasil ditambahkan.');
    }

    public function show(TemuanAmi $temuan)
    {
        $temuan->load([
            'jadwalAmi.prodi',
            'auditor.user',
            'tindakLanjut.user',
            'tindakLanjut.reviewer',
        ]);
        
        return view('admin.ami.temuan.show', compact('temuan'));
    }

    public function edit(TemuanAmi $temuan)
    {
        $jadwals = JadwalAmi::with('prodi')
            ->whereIn('status', ['berlangsung', 'selesai'])
            ->orderByDesc('tanggal_audit')
            ->get();
        
        $auditors = $temuan->jadwalAmi->penugasan()->with('auditor.user')->get()->pluck('auditor');
        
        $kategoriOptions = ['mayor', 'minor', 'observasi', 'rekomendasi'];
        $standarOptions = [
            'Standar 1 - Visi, Misi, Tujuan dan Strategi',
            'Standar 2 - Tata Pamong, Tata Kelola dan Kerjasama',
            'Standar 3 - Mahasiswa',
            'Standar 4 - Sumber Daya Manusia',
            'Standar 5 - Keuangan, Sarana dan Prasarana',
            'Standar 6 - Pendidikan',
            'Standar 7 - Penelitian',
            'Standar 8 - Pengabdian kepada Masyarakat',
            'Standar 9 - Luaran dan Capaian Tridharma',
        ];
        
        return view('admin.ami.temuan.edit', compact(
            'temuan',
            'jadwals', 
            'auditors', 
            'kategoriOptions', 
            'standarOptions'
        ));
    }

    public function update(Request $request, TemuanAmi $temuan)
    {
        $validated = $request->validate([
            'jadwal_ami_id' => 'required|exists:jadwal_ami,id',
            'auditor_id' => 'required|exists:auditor,id',
            'standar' => 'required|string|max:255',
            'kategori' => 'required|in:mayor,minor,observasi,rekomendasi',
            'deskripsi' => 'required|string',
            'bukti' => 'nullable|string',
            'akar_masalah' => 'nullable|string',
            'rekomendasi' => 'required|string',
            'batas_tindak_lanjut' => 'nullable|date',
            'status' => 'required|in:open,in_progress,closed,verified',
        ]);

        $temuan->update($validated);

        return redirect()->route('admin.ami.temuan.show', $temuan)
            ->with('success', 'Temuan berhasil diperbarui.');
    }

    public function destroy(TemuanAmi $temuan)
    {
        if ($temuan->tindakLanjut()->count() > 0) {
            return redirect()->route('admin.ami.temuan.index')
                ->with('error', 'Temuan tidak dapat dihapus karena sudah ada tindak lanjut.');
        }
        
        $temuan->delete();

        return redirect()->route('admin.ami.temuan.index')
            ->with('success', 'Temuan berhasil dihapus.');
    }

    /**
     * Verify a temuan
     */
    public function verify(TemuanAmi $temuan)
    {
        $temuan->verify();

        return back()->with('success', 'Temuan berhasil diverifikasi.');
    }

    /**
     * Close a temuan
     */
    public function close(TemuanAmi $temuan)
    {
        $temuan->close();

        return back()->with('success', 'Temuan berhasil ditutup.');
    }

    /**
     * Reopen a temuan
     */
    public function reopen(TemuanAmi $temuan)
    {
        $temuan->update(['status' => 'in_progress']);

        return back()->with('success', 'Temuan berhasil dibuka kembali.');
    }
}
