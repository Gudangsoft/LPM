<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditButir;
use App\Models\PeriodeAmi;
use App\Models\Rtm;
use App\Models\RtmAgenda;
use App\Models\RtmKeputusan;
use App\Models\TemuanAmi;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RtmController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:rtm.view', only: ['index', 'show']),
            new Middleware('permission:rtm.manage', only: [
                'create', 'store', 'edit', 'update', 'destroy', 'generateRingkasan',
                'addAgenda', 'updateAgenda', 'deleteAgenda',
                'addKeputusan', 'updateKeputusan', 'deleteKeputusan',
            ]),
        ];
    }

    public function index(Request $request)
    {
        $rtm = Rtm::with('periodeAmi')
            ->withCount('keputusan')
            ->when($request->filled('periode_id'), fn ($q) => $q->where('periode_ami_id', $request->periode_id))
            ->latest('tanggal')->latest('id')
            ->paginate(15)->withQueryString();

        $periodes = PeriodeAmi::orderByDesc('tanggal_mulai')->get();

        return view('admin.ami.rtm.index', compact('rtm', 'periodes'));
    }

    public function create()
    {
        return view('admin.ami.rtm.create', ['periodes' => PeriodeAmi::orderByDesc('tanggal_mulai')->get()]);
    }

    public function store(Request $request)
    {
        $rtm = Rtm::create($this->validated($request));

        return redirect()->route('admin.ami.rtm.show', $rtm)->with('success', 'RTM dibuat.');
    }

    public function show(Rtm $rtm)
    {
        $rtm->load('periodeAmi', 'agenda', 'keputusan');

        return view('admin.ami.rtm.show', compact('rtm'));
    }

    public function edit(Rtm $rtm)
    {
        return view('admin.ami.rtm.edit', ['rtm' => $rtm, 'periodes' => PeriodeAmi::orderByDesc('tanggal_mulai')->get()]);
    }

    public function update(Request $request, Rtm $rtm)
    {
        $rtm->update($this->validated($request));

        return redirect()->route('admin.ami.rtm.show', $rtm)->with('success', 'RTM diperbarui.');
    }

    public function destroy(Rtm $rtm)
    {
        $rtm->delete();

        return redirect()->route('admin.ami.rtm.index')->with('success', 'RTM dihapus.');
    }

    /** Isi ringkasan otomatis dari data AMI periode terkait. */
    public function generateRingkasan(Rtm $rtm)
    {
        $periodeId = $rtm->periode_ami_id;

        $temuan = TemuanAmi::query()
            ->when($periodeId, fn ($q) => $q->whereHas('jadwalAmi', fn ($j) => $j->where('periode_ami_id', $periodeId)));

        $total = (clone $temuan)->count();
        $selesai = (clone $temuan)->whereIn('status', ['closed', 'verified'])->count();
        $mayor = (clone $temuan)->where('kategori', 'mayor')->count();
        $minor = (clone $temuan)->where('kategori', 'minor')->count();

        $rtl = TindakLanjut::query()
            ->when($periodeId, fn ($q) => $q->whereHas('temuanAmi.jadwalAmi', fn ($j) => $j->where('periode_ami_id', $periodeId)));
        $rtlTotal = (clone $rtl)->count();
        $rtlApproved = (clone $rtl)->where('status', 'approved')->count();

        $perStandar = (clone $temuan)
            ->leftJoin('standar_mutu', 'temuan_ami.standar_mutu_id', '=', 'standar_mutu.id')
            ->selectRaw('COALESCE(standar_mutu.nama, temuan_ami.standar) as label, count(*) as n')
            ->groupBy('label')->orderByDesc('n')->limit(5)->pluck('n', 'label');

        $lines = [
            "Rekapitulasi Hasil AMI" . ($rtm->periodeAmi ? " — {$rtm->periodeAmi->nama}" : ''),
            "",
            "Total temuan: {$total} (Mayor: {$mayor}, Minor: {$minor}).",
            "Temuan selesai/ditutup: {$selesai} dari {$total}.",
            "Penyelesaian RTL: {$rtlApproved} dari {$rtlTotal} disetujui" . ($rtlTotal > 0 ? " (" . round($rtlApproved / $rtlTotal * 100) . "%)." : "."),
            "",
            "Temuan terbanyak per standar:",
        ];
        foreach ($perStandar as $label => $n) {
            $lines[] = "  - {$label}: {$n} temuan";
        }
        if ($perStandar->isEmpty()) {
            $lines[] = "  - (tidak ada temuan)";
        }

        $rtm->update(['ringkasan' => implode("\n", $lines)]);

        return back()->with('success', 'Ringkasan bahan RTM di-generate dari data AMI.');
    }

    // -- agenda --------------------------------------------------------------

    public function addAgenda(Request $request, Rtm $rtm)
    {
        $data = $request->validate(['topik' => 'required|string|max:255', 'pembahasan' => 'nullable|string']);
        $data['urutan'] = (int) $rtm->agenda()->max('urutan') + 1;
        $rtm->agenda()->create($data);

        return back()->with('success', 'Agenda ditambahkan.');
    }

    public function updateAgenda(Request $request, RtmAgenda $agenda)
    {
        $agenda->update($request->validate(['topik' => 'required|string|max:255', 'pembahasan' => 'nullable|string']));

        return back()->with('success', 'Agenda diperbarui.');
    }

    public function deleteAgenda(RtmAgenda $agenda)
    {
        $agenda->delete();

        return back()->with('success', 'Agenda dihapus.');
    }

    // -- keputusan --------------------------------------------------------------

    public function addKeputusan(Request $request, Rtm $rtm)
    {
        $data = $this->validatedKeputusan($request);
        $data['urutan'] = (int) $rtm->keputusan()->max('urutan') + 1;
        $rtm->keputusan()->create($data);

        return back()->with('success', 'Keputusan/rekomendasi ditambahkan.');
    }

    public function updateKeputusan(Request $request, RtmKeputusan $keputusan)
    {
        $keputusan->update($this->validatedKeputusan($request));

        return back()->with('success', 'Keputusan diperbarui.');
    }

    public function deleteKeputusan(RtmKeputusan $keputusan)
    {
        $keputusan->delete();

        return back()->with('success', 'Keputusan dihapus.');
    }

    // ---------------------------------------------------------------------

    private function validated(Request $request): array
    {
        return $request->validate([
            'judul' => 'required|string|max:255',
            'periode_ami_id' => 'nullable|exists:periode_ami,id',
            'tanggal' => 'nullable|date',
            'tempat' => 'nullable|string|max:255',
            'pemimpin' => 'nullable|string|max:255',
            'notulen' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,selesai',
            'ringkasan' => 'nullable|string',
        ]);
    }

    private function validatedKeputusan(Request $request): array
    {
        return $request->validate([
            'keputusan' => 'required|string',
            'rekomendasi' => 'nullable|string',
            'pic' => 'nullable|string|max:255',
            'target_tanggal' => 'nullable|date',
            'status' => 'nullable|in:belum,proses,selesai',
            'tindak_lanjut' => 'nullable|string',
        ]);
    }
}
