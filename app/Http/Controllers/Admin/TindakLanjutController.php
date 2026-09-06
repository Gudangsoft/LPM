<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TindakLanjut;
use App\Models\TemuanAmi;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class TindakLanjutController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:tindak-lanjut.view', only: ['index', 'show', 'pendingReview']),
            new Middleware('permission:tindak-lanjut.submit', only: ['create', 'store', 'edit', 'update', 'destroy']),
            new Middleware('permission:tindak-lanjut.review', only: ['review']),
        ];
    }

    /**
     * A permission only proves the role may submit *something* - this confirms
     * the record itself belongs to the acting user (or they're admin).
     */
    private function authorizeOwner(TindakLanjut $tindakLanjut): void
    {
        abort_unless(
            auth()->user()->isAdmin() || $tindakLanjut->user_id === auth()->id(),
            403,
            'Anda tidak memiliki izin untuk mengakses tindak lanjut ini.'
        );
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $scoped = !$user->isAdmin() && $user->isKaprodi();

        $query = TindakLanjut::with(['temuanAmi.jadwalAmi.prodi', 'user', 'reviewer'])
            ->latest()
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user));

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('temuan_id') && $request->temuan_id) {
            $query->where('temuan_ami_id', $request->temuan_id);
        }

        if ($request->has('prodi_id') && $request->prodi_id) {
            $query->whereHas('temuanAmi.jadwalAmi', fn ($q) => $q->where('prodi_id', $request->prodi_id));
        }

        if ($request->has('search') && $request->search) {
            $query->where('deskripsi', 'like', "%{$request->search}%");
        }

        $tindakLanjuts = $query->paginate(15);
        $prodis = Prodi::active()->orderBy('nama')->get();
        $statusOptions = ['submitted', 'reviewed', 'approved', 'rejected'];
        $pendingCount = TindakLanjut::pendingReview()->when($scoped, fn ($q) => $q->ownedByKaprodi($user))->count();
        $stats = [
            'submitted' => TindakLanjut::where('status', 'submitted')->when($scoped, fn ($q) => $q->ownedByKaprodi($user))->count(),
            'reviewed' => TindakLanjut::where('status', 'reviewed')->when($scoped, fn ($q) => $q->ownedByKaprodi($user))->count(),
            'approved' => TindakLanjut::where('status', 'approved')->when($scoped, fn ($q) => $q->ownedByKaprodi($user))->count(),
            'rejected' => TindakLanjut::where('status', 'rejected')->when($scoped, fn ($q) => $q->ownedByKaprodi($user))->count(),
        ];

        return view('admin.ami.tindak-lanjut.index', compact('tindakLanjuts', 'prodis', 'statusOptions', 'pendingCount', 'stats'));
    }

    public function create(Request $request)
    {
        $temuanId = $request->get('temuan_id');
        $temuan = $temuanId ? TemuanAmi::with('jadwalAmi.prodi')->find($temuanId) : null;

        $temuans = TemuanAmi::with('jadwalAmi.prodi')
            ->whereIn('status', ['open', 'in_progress'])
            ->when(!auth()->user()->isAdmin() && auth()->user()->isKaprodi(), function ($query) {
                $query->whereHas('jadwalAmi.prodi', fn ($q) => $q->where('kaprodi_id', auth()->id()));
            })
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

        if (!auth()->user()->isAdmin() && auth()->user()->isKaprodi()) {
            $temuan = TemuanAmi::with('jadwalAmi.prodi')->findOrFail($validated['temuan_ami_id']);
            abort_unless(
                $temuan->jadwalAmi?->prodi?->kaprodi_id === auth()->id(),
                403,
                'Anda hanya dapat mengajukan tindak lanjut untuk program studi Anda sendiri.'
            );
        }

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
        $user = auth()->user();
        if (!$user->isAdmin() && $user->isKaprodi()) {
            abort_unless(
                $tindakLanjut->temuanAmi?->jadwalAmi?->prodi?->kaprodi_id === $user->id,
                403,
                'Anda hanya dapat melihat tindak lanjut program studi Anda sendiri.'
            );
        }

        $tindakLanjut->load(['temuanAmi.jadwalAmi.prodi', 'temuanAmi.auditor.user', 'user', 'reviewer']);

        return view('admin.ami.tindak-lanjut.show', compact('tindakLanjut'));
    }

    public function edit(TindakLanjut $tindakLanjut)
    {
        $this->authorizeOwner($tindakLanjut);

        if (!in_array($tindakLanjut->status, ['submitted', 'rejected'])) {
            return redirect()->route('admin.ami.tindak-lanjut.show', $tindakLanjut)
                ->with('error', 'Tindak lanjut tidak dapat diedit pada status ini.');
        }
        
        return view('admin.ami.tindak-lanjut.edit', compact('tindakLanjut'));
    }

    public function update(Request $request, TindakLanjut $tindakLanjut)
    {
        $this->authorizeOwner($tindakLanjut);

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
        $this->authorizeOwner($tindakLanjut);

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
        $user = auth()->user();
        $tindakLanjuts = TindakLanjut::with(['temuanAmi.jadwalAmi.prodi', 'user'])
            ->pendingReview()
            ->when(!$user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->ownedByKaprodi($user))
            ->latest()
            ->paginate(15);

        return view('admin.ami.tindak-lanjut.pending', compact('tindakLanjuts'));
    }
}
