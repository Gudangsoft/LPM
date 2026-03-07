<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Statistik;
use Illuminate\Http\Request;

class StatistikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Statistik::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $statistiks = $query->orderBy('tahun', 'desc')
            ->orderBy('kategori')
            ->paginate(20);

        $kategoriOptions = Statistik::getKategoriOptions();
        $years = Statistik::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('admin.statistik.index', compact('statistiks', 'kategoriOptions', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoriOptions = Statistik::getKategoriOptions();
        return view('admin.statistik.create', compact('kategoriOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|in:' . implode(',', array_keys(Statistik::getKategoriOptions())),
            'tahun' => 'required|integer|min:2000|max:' . (now()->year + 5),
            'usulan' => 'required|integer|min:0',
            'didanai' => 'required|integer|min:0',
            'dana_usulan' => 'nullable|numeric|min:0',
            'dana_disetujui' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Check if already exists
        $existing = Statistik::where('kategori', $validated['kategori'])
            ->where('tahun', $validated['tahun'])
            ->first();

        if ($existing) {
            return back()->withInput()->withErrors(['tahun' => 'Data untuk kategori dan tahun ini sudah ada.']);
        }

        Statistik::create($validated);

        return redirect()->route('admin.statistik.index')
            ->with('success', 'Data statistik berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Statistik $statistik)
    {
        return view('admin.statistik.show', compact('statistik'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Statistik $statistik)
    {
        $kategoriOptions = Statistik::getKategoriOptions();
        return view('admin.statistik.edit', compact('statistik', 'kategoriOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Statistik $statistik)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|in:' . implode(',', array_keys(Statistik::getKategoriOptions())),
            'tahun' => 'required|integer|min:2000|max:' . (now()->year + 5),
            'usulan' => 'required|integer|min:0',
            'didanai' => 'required|integer|min:0',
            'dana_usulan' => 'nullable|numeric|min:0',
            'dana_disetujui' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Check if already exists (excluding current record)
        $existing = Statistik::where('kategori', $validated['kategori'])
            ->where('tahun', $validated['tahun'])
            ->where('id', '!=', $statistik->id)
            ->first();

        if ($existing) {
            return back()->withInput()->withErrors(['tahun' => 'Data untuk kategori dan tahun ini sudah ada.']);
        }

        $statistik->update($validated);

        return redirect()->route('admin.statistik.index')
            ->with('success', 'Data statistik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Statistik $statistik)
    {
        $statistik->delete();

        return redirect()->route('admin.statistik.index')
            ->with('success', 'Data statistik berhasil dihapus.');
    }

    /**
     * Display chart view
     */
    public function chart(Request $request)
    {
        $kategori = $request->get('kategori', Statistik::KATEGORI_PENELITIAN);
        $fromYear = $request->get('from_year', now()->year - 10);
        $toYear = $request->get('to_year', now()->year);

        $chartData = Statistik::getChartData($kategori, $fromYear, $toYear);
        $summary = Statistik::getSummary($kategori);
        $kategoriOptions = Statistik::getKategoriOptions();

        return view('admin.statistik.chart', compact('chartData', 'summary', 'kategoriOptions', 'kategori', 'fromYear', 'toYear'));
    }

    /**
     * Import data (bulk create/update)
     */
    public function import(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'data.*.kategori' => 'required|string',
            'data.*.tahun' => 'required|integer',
            'data.*.usulan' => 'required|integer|min:0',
            'data.*.didanai' => 'required|integer|min:0',
        ]);

        foreach ($request->data as $row) {
            Statistik::updateOrCreate(
                ['kategori' => $row['kategori'], 'tahun' => $row['tahun']],
                [
                    'usulan' => $row['usulan'],
                    'didanai' => $row['didanai'],
                    'dana_usulan' => $row['dana_usulan'] ?? 0,
                    'dana_disetujui' => $row['dana_disetujui'] ?? 0,
                ]
            );
        }

        return redirect()->route('admin.statistik.index')
            ->with('success', 'Data statistik berhasil diimport.');
    }
}
