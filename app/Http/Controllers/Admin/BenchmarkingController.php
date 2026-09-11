<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Benchmarking;
use App\Models\Prodi;
use App\Models\StandarMutu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BenchmarkingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:benchmarking.view', only: ['index', 'show']),
            new Middleware('permission:benchmarking.manage', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Benchmarking::with(['standarMutu', 'prodi'])
            ->when($request->filled('prodi_id'), fn ($q) => $q->where('prodi_id', $request->prodi_id))
            ->when($request->filled('tahun_akademik'), fn ($q) => $q->where('tahun_akademik', $request->tahun_akademik))
            ->when($request->filled('search'), fn ($q) => $q->where('aspek', 'like', "%{$request->search}%"))
            ->orderByDesc('tahun_akademik');

        $benchmarking = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => (clone $query)->count(),
            'institusi' => (clone $query)->distinct('institusi_pembanding')->count('institusi_pembanding'),
            'prodi' => (clone $query)->whereNotNull('prodi_id')->count(),
        ];

        $prodiOptions = Prodi::orderBy('nama')->get();
        $tahunOptions = Benchmarking::query()->distinct()->orderByDesc('tahun_akademik')->pluck('tahun_akademik');

        return view('admin.ami.benchmarking.index', compact('benchmarking', 'stats', 'prodiOptions', 'tahunOptions'));
    }

    public function create()
    {
        $standarOptions = StandarMutu::orderBy('urutan')->get();
        $prodiOptions = Prodi::orderBy('nama')->get();

        return view('admin.ami.benchmarking.create', compact('standarOptions', 'prodiOptions'));
    }

    public function store(Request $request)
    {
        Benchmarking::create($this->validated($request));

        return redirect()->route('admin.ami.benchmarking.index')->with('success', 'Data benchmarking berhasil ditambahkan.');
    }

    public function edit(Benchmarking $benchmarking)
    {
        $standarOptions = StandarMutu::orderBy('urutan')->get();
        $prodiOptions = Prodi::orderBy('nama')->get();

        return view('admin.ami.benchmarking.edit', compact('benchmarking', 'standarOptions', 'prodiOptions'));
    }

    public function update(Request $request, Benchmarking $benchmarking)
    {
        $benchmarking->update($this->validated($request));

        return redirect()->route('admin.ami.benchmarking.index')->with('success', 'Data benchmarking berhasil diperbarui.');
    }

    public function destroy(Benchmarking $benchmarking)
    {
        $benchmarking->delete();

        return redirect()->route('admin.ami.benchmarking.index')->with('success', 'Data benchmarking dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'standar_mutu_id' => 'nullable|exists:standar_mutu,id',
            'prodi_id' => 'nullable|exists:prodi,id',
            'tahun_akademik' => 'required|string|max:20',
            'aspek' => 'required|string|max:255',
            'institusi_pembanding' => 'required|string|max:255',
            'nilai_sendiri' => 'nullable|string|max:255',
            'nilai_pembanding' => 'nullable|string|max:255',
            'kesimpulan' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
        ]);
    }
}
