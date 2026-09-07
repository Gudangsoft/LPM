<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditButir;
use App\Models\BuktiAudit;
use App\Models\ButirInstrumen;
use App\Models\EvaluasiDiri;
use App\Models\JadwalAmi;
use App\Models\PeriodeAmi;
use App\Models\StandarMutu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EvaluasiDiriController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:evaluasi-diri.view', only: ['index', 'show']),
            new Middleware('permission:evaluasi-diri.fill', only: ['save', 'submit', 'addBukti', 'deleteBukti']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $scoped = ! $user->isAdmin() && $user->isKaprodi();

        $periodes = PeriodeAmi::orderByDesc('tanggal_mulai')->get();

        $jadwals = JadwalAmi::with(['prodi', 'periodeAmi', 'evaluasiDiri'])
            ->when($request->filled('periode_id'), fn ($q) => $q->where('periode_ami_id', $request->periode_id))
            ->when($scoped, fn ($q) => $q->ownedByKaprodi($user))
            ->orderByDesc('tanggal_audit')
            ->paginate(15)->withQueryString();

        return view('admin.ami.evaluasi-diri.index', compact('jadwals', 'periodes'));
    }

    public function show(JadwalAmi $jadwal)
    {
        $jadwal->load('prodi', 'periodeAmi');
        $this->ensureButirRows($jadwal);

        $header = EvaluasiDiri::firstOrCreate(['jadwal_ami_id' => $jadwal->id]);

        $standar = StandarMutu::active()->ordered()
            ->with(['butir' => fn ($q) => $q->where('is_active', true)->orderBy('urutan')])
            ->get()
            ->filter(fn ($s) => $s->butir->isNotEmpty());

        $rows = AuditButir::where('jadwal_ami_id', $jadwal->id)
            ->with('bukti.uploader')
            ->get()->keyBy('butir_instrumen_id');

        $editable = $this->isEditable($jadwal, $header);

        return view('admin.ami.evaluasi-diri.show', compact('jadwal', 'header', 'standar', 'rows', 'editable'));
    }

    public function save(Request $request, JadwalAmi $jadwal)
    {
        $header = EvaluasiDiri::firstOrCreate(['jadwal_ami_id' => $jadwal->id]);
        $this->assertEditable($jadwal, $header);

        $data = $request->validate([
            'catatan' => 'nullable|string',
            'butir' => 'array',
            'butir.*.nilai_mandiri' => 'nullable|numeric|min:0|max:100',
            'butir.*.deskripsi_capaian' => 'nullable|string',
        ]);

        foreach ($data['butir'] ?? [] as $auditButirId => $vals) {
            AuditButir::where('id', $auditButirId)
                ->where('jadwal_ami_id', $jadwal->id)
                ->update([
                    'nilai_mandiri' => $vals['nilai_mandiri'] ?? null,
                    'deskripsi_capaian' => $vals['deskripsi_capaian'] ?? null,
                    'diisi_auditee_at' => now(),
                ]);
        }

        $header->update(['catatan' => $data['catatan'] ?? null]);

        return redirect()->route('admin.ami.evaluasi-diri.show', $jadwal)
            ->with('success', 'Evaluasi diri tersimpan.');
    }

    public function submit(Request $request, JadwalAmi $jadwal)
    {
        $header = EvaluasiDiri::firstOrCreate(['jadwal_ami_id' => $jadwal->id]);
        $this->assertCanFill($jadwal);

        $header->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'submitted_by' => auth()->id(),
        ]);

        return redirect()->route('admin.ami.evaluasi-diri.show', $jadwal)
            ->with('success', 'Evaluasi diri berhasil dikirim.');
    }

    public function addBukti(Request $request, AuditButir $auditButir)
    {
        $jadwal = $auditButir->jadwalAmi;
        $header = EvaluasiDiri::firstOrCreate(['jadwal_ami_id' => $jadwal->id]);
        $this->assertEditable($jadwal, $header);

        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'tautan' => 'nullable|url|max:2048',
            'keterangan' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',
        ]);

        if (blank($data['tautan'] ?? null) && ! $request->hasFile('file')) {
            return back()->with('error', 'Lampirkan berkas atau isi tautan bukti.');
        }

        $versi = (int) BuktiAudit::where('audit_butir_id', $auditButir->id)->max('versi') + 1;

        BuktiAudit::create([
            'audit_butir_id' => $auditButir->id,
            'judul' => $data['judul'],
            'tautan' => $data['tautan'] ?? null,
            'file_path' => $request->hasFile('file') ? $request->file('file')->store('bukti-audit', 'public') : null,
            'keterangan' => $data['keterangan'] ?? null,
            'versi' => $versi,
            'diunggah_oleh' => auth()->id(),
        ]);

        return back()->with('success', 'Bukti ditambahkan.');
    }

    public function deleteBukti(BuktiAudit $bukti)
    {
        $jadwal = $bukti->auditButir->jadwalAmi;
        $header = EvaluasiDiri::firstOrCreate(['jadwal_ami_id' => $jadwal->id]);
        $this->assertEditable($jadwal, $header);

        $bukti->delete();

        return back()->with('success', 'Bukti dihapus.');
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

    private function assertCanFill(JadwalAmi $jadwal): void
    {
        $user = auth()->user();
        abort_unless(
            $user->isAdmin() || $jadwal->prodi?->kaprodi_id === $user->id,
            403,
            'Hanya kepala program studi unit ini yang dapat mengisi evaluasi diri.'
        );
    }

    private function isEditable(JadwalAmi $jadwal, EvaluasiDiri $header): bool
    {
        $user = auth()->user();

        if (! $user->isAdmin() && $jadwal->prodi?->kaprodi_id !== $user->id) {
            return false;
        }

        return $user->isAdmin() || ! $header->isSubmitted();
    }

    private function assertEditable(JadwalAmi $jadwal, EvaluasiDiri $header): void
    {
        $this->assertCanFill($jadwal);

        abort_if(
            $header->isSubmitted() && ! auth()->user()->isAdmin(),
            403,
            'Evaluasi diri sudah dikirim dan tidak dapat diubah.'
        );
    }
}
