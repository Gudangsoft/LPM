<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TindakLanjut;
use App\Models\TemuanAmi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TindakLanjutController extends Controller
{
    public function index(Request $request)
    {
        $query = TindakLanjut::with(['temuanAmi.jadwalAmi.prodi', 'user', 'reviewer'])
            ->latest();
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('temuan_id') && $request->temuan_id) {
            $query->where('temuan_ami_id', $request->temuan_id);
        }
        
        $tindakLanjuts = $query->paginate(15);
        $statusOptions = ['submitted', 'reviewed', 'approved', 'rejected'];
        
        return view('admin.ami.tindak-lanjut.index', compact('tindakLanjuts', 'statusOptions'));
    }

    public function create(Request $request)
    {
        $temuanId = $request->get('temuan_id');
        $temuan = $temuanId ? TemuanAmi::with('jadwalAmi.prodi')->find($temuanId) : null;
        
        $temuans = TemuanAmi::with('jadwalAmi.prodi')
            ->whereIn('status', ['open', 'in_progress'])
            ->orderByDesc('created_at')
            ->get();
        
        return view('admin.ami.tindak-lanjut.create', compact('temuan', 'temuans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'temuan_ami_id' => 'required|exists:temuan_ami,id',
            'deskripsi' => 'required|string',
            'file_bukti' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'tanggal_submit' => 'required|date',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'submitted';

        if ($request->hasFile('file_bukti')) {
            $validated['file_bukti'] = $request->file('file_bukti')->store('tindak-lanjut', 'public');
        }

        $tindakLanjut = TindakLanjut::create($validated);

        // Update temuan status to in_progress
        $tindakLanjut->temuanAmi->update(['status' => 'in_progress']);

        return redirect()->route('admin.ami.tindak-lanjut.show', $tindakLanjut)
            ->with('success', 'Tindak lanjut berhasil disubmit.');
    }

    public function show(TindakLanjut $tindakLanjut)
    {
        $tindakLanjut->load(['temuanAmi.jadwalAmi.prodi', 'temuanAmi.auditor.user', 'user', 'reviewer']);
        
        return view('admin.ami.tindak-lanjut.show', compact('tindakLanjut'));
    }

    public function edit(TindakLanjut $tindakLanjut)
    {
        if (!in_array($tindakLanjut->status, ['submitted', 'rejected'])) {
            return redirect()->route('admin.ami.tindak-lanjut.show', $tindakLanjut)
                ->with('error', 'Tindak lanjut tidak dapat diedit pada status ini.');
        }
        
        return view('admin.ami.tindak-lanjut.edit', compact('tindakLanjut'));
    }

    public function update(Request $request, TindakLanjut $tindakLanjut)
    {
        if (!in_array($tindakLanjut->status, ['submitted', 'rejected'])) {
            return redirect()->route('admin.ami.tindak-lanjut.show', $tindakLanjut)
                ->with('error', 'Tindak lanjut tidak dapat diedit pada status ini.');
        }
        
        $validated = $request->validate([
            'deskripsi' => 'required|string',
            'file_bukti' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'tanggal_submit' => 'required|date',
        ]);

        // Reset status to submitted if it was rejected
        if ($tindakLanjut->status === 'rejected') {
            $validated['status'] = 'submitted';
            $validated['catatan_reviewer'] = null;
        }

        if ($request->hasFile('file_bukti')) {
            if ($tindakLanjut->file_bukti) {
                Storage::disk('public')->delete($tindakLanjut->file_bukti);
            }
            $validated['file_bukti'] = $request->file('file_bukti')->store('tindak-lanjut', 'public');
        }

        $tindakLanjut->update($validated);

        return redirect()->route('admin.ami.tindak-lanjut.show', $tindakLanjut)
            ->with('success', 'Tindak lanjut berhasil diperbarui.');
    }

    public function destroy(TindakLanjut $tindakLanjut)
    {
        if (!in_array($tindakLanjut->status, ['submitted', 'rejected'])) {
            return redirect()->route('admin.ami.tindak-lanjut.index')
                ->with('error', 'Tindak lanjut tidak dapat dihapus pada status ini.');
        }
        
        if ($tindakLanjut->file_bukti) {
            Storage::disk('public')->delete($tindakLanjut->file_bukti);
        }
        
        $tindakLanjut->delete();

        return redirect()->route('admin.ami.tindak-lanjut.index')
            ->with('success', 'Tindak lanjut berhasil dihapus.');
    }

    /**
     * Review tindak lanjut
     */
    public function review(Request $request, TindakLanjut $tindakLanjut)
    {
        if ($tindakLanjut->status !== 'submitted') {
            return back()->with('error', 'Tindak lanjut sudah direview.');
        }
        
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan_reviewer' => 'nullable|string',
        ]);

        if ($validated['action'] === 'approve') {
            $tindakLanjut->approve(auth()->user(), $validated['catatan_reviewer']);
            $message = 'Tindak lanjut berhasil disetujui.';
        } else {
            if (empty($validated['catatan_reviewer'])) {
                return back()->with('error', 'Catatan wajib diisi untuk penolakan.');
            }
            $tindakLanjut->reject(auth()->user(), $validated['catatan_reviewer']);
            $message = 'Tindak lanjut ditolak.';
        }

        return back()->with('success', $message);
    }

    /**
     * Pending review list
     */
    public function pendingReview()
    {
        $tindakLanjuts = TindakLanjut::with(['temuanAmi.jadwalAmi.prodi', 'user'])
            ->pendingReview()
            ->latest()
            ->paginate(15);
        
        return view('admin.ami.tindak-lanjut.pending', compact('tindakLanjuts'));
    }
}
