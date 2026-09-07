<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditButir;
use App\Models\BuktiAudit;
use App\Models\ButirInstrumen;
use App\Models\JadwalAmi;
use App\Models\PeriodeAmi;
use App\Models\StandarMutu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LembarAuditController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lembar-audit.view', only: ['index', 'show']),
            new Middleware('permission:lembar-audit.fill', only: ['save', 'validateBukti']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $onlyMine = ! $user->isAdmin() && $user->isAuditor();

        $periodes = PeriodeAmi::orderByDesc('tanggal_mulai')->get();

        $jadwals = JadwalAmi::with(['prodi', 'periodeAmi', 'evaluasiDiri'])
            ->withCount([
                'auditButir as terisi_auditor_count' => fn ($q) => $q->whereNotNull('nilai_auditor'),
                'auditButir as total_butir_count',
            ])
            ->when($request->filled('periode_id'), fn ($q) => $q->where('periode_ami_id', $request->periode_id))
            ->when($onlyMine, fn ($q) => $q->assignedToUser($user->id))
            ->when(! $user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->ownedByKaprodi($user))
            ->orderByDesc('tanggal_audit')
            ->paginate(15)->withQueryString();

        return view('admin.ami.lembar-audit.index', compact('jadwals', 'periodes'));
    }

    public function show(JadwalAmi $jadwal)
    {
        $jadwal->load('prodi', 'periodeAmi', 'evaluasiDiri', 'penugasan.auditor.user');
        $this->ensureButirRows($jadwal);

        $standar = StandarMutu::active()->ordered()
            ->with(['butir' => fn ($q) => $q->where('is_active', true)->orderBy('urutan')])
            ->get()
            ->filter(fn ($s) => $s->butir->isNotEmpty());

        $rows = AuditButir::where('jadwal_ami_id', $jadwal->id)
            ->with('bukti.uploader')
            ->get()->keyBy('butir_instrumen_id');

        $editable = $this->canFill($jadwal);
        $statusOptions = ['belum' => 'Belum dinilai', 'sesuai' => 'Sesuai', 'perlu_perbaikan' => 'Perlu Perbaikan', 'tidak_sesuai' => 'Tidak Sesuai'];

        return view('admin.ami.lembar-audit.show', compact('jadwal', 'standar', 'rows', 'editable', 'statusOptions'));
    }

    public function save(Request $request, JadwalAmi $jadwal)
    {
        $this->assertCanFill($jadwal);

        $data = $request->validate([
            'butir' => 'array',
            'butir.*.nilai_auditor' => 'nullable|numeric|min:0|max:100',
            'butir.*.catatan_auditor' => 'nullable|string',
            'butir.*.status_verifikasi' => 'nullable|in:belum,sesuai,perlu_perbaikan,tidak_sesuai',
        ]);

        foreach ($data['butir'] ?? [] as $auditButirId => $vals) {
            AuditButir::where('id', $auditButirId)
                ->where('jadwal_ami_id', $jadwal->id)
                ->update([
                    'nilai_auditor' => $vals['nilai_auditor'] ?? null,
                    'catatan_auditor' => $vals['catatan_auditor'] ?? null,
                    'status_verifikasi' => $vals['status_verifikasi'] ?? 'belum',
                    'diisi_auditor_at' => now(),
                ]);
        }

        return redirect()->route('admin.ami.lembar-audit.show', $jadwal)
            ->with('success', 'Lembar kerja audit tersimpan.');
    }

    public function validateBukti(Request $request, BuktiAudit $bukti)
    {
        $jadwal = $bukti->auditButir->jadwalAmi;
        $this->assertCanFill($jadwal);

        $data = $request->validate([
            'status_validasi' => 'required|in:belum,valid,tidak_valid',
            'catatan_validasi' => 'nullable|string|max:1000',
        ]);

        $bukti->update($data);

        return back()->with('success', 'Status validasi bukti diperbarui.');
    }

    // ---------------------------------------------------------------------

    private function ensureButirRows(JadwalAmi $jadwal): void
    {
        $butirIds = ButirInstrumen::where('is_active', true)
            ->whereHas('standarMutu', fn ($q) => $q->where('is_active', true))
            ->pluck('id');

        $existing = AuditButir::where('jadwal_ami_id', $jadwal->id)->pluck('butir_instrumen_id')->all();

        foreach ($butirIds->diff($existing) as $bid) {
            AuditButir::create(['jadwal_ami_id' => $jadwal->id, 'butir_instrumen_id' => $bid]);
        }
    }

    private function canFill(JadwalAmi $jadwal): bool
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return true;
        }

        return $jadwal->penugasan()
            ->whereHas('auditor', fn ($q) => $q->where('user_id', $user->id))
            ->exists();
    }

    private function assertCanFill(JadwalAmi $jadwal): void
    {
        abort_unless(
            $this->canFill($jadwal),
            403,
            'Hanya auditor yang ditugaskan pada jadwal ini yang dapat mengisi lembar kerja audit.'
        );
    }
}
