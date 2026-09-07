<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ButirInstrumen;
use App\Models\StandarMutu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ButirInstrumenController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:standar-mutu.view', only: ['index']),
            new Middleware('permission:standar-mutu.manage', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $standarMutus = StandarMutu::ordered()->get();

        $query = ButirInstrumen::with('standarMutu')
            ->join('standar_mutu', 'butir_instrumen.standar_mutu_id', '=', 'standar_mutu.id')
            ->orderBy('standar_mutu.urutan')
            ->orderBy('butir_instrumen.urutan')
            ->select('butir_instrumen.*');

        if ($request->filled('standar_mutu_id')) {
            $query->where('butir_instrumen.standar_mutu_id', $request->standar_mutu_id);
        }

        if ($request->filled('search')) {
            $query->where('butir_instrumen.pertanyaan', 'like', "%{$request->search}%");
        }

        $butir = $query->paginate(20)->withQueryString();

        return view('admin.ami.instrumen.index', compact('butir', 'standarMutus'));
    }

    public function create()
    {
        $standarMutus = StandarMutu::ordered()->get();

        return view('admin.ami.instrumen.create', compact('standarMutus'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        ButirInstrumen::create($data);

        return redirect()->route('admin.ami.instrumen.index', ['standar_mutu_id' => $data['standar_mutu_id']])
            ->with('success', 'Butir instrumen berhasil ditambahkan.');
    }

    public function edit(ButirInstrumen $instrumen)
    {
        $standarMutus = StandarMutu::ordered()->get();

        return view('admin.ami.instrumen.edit', ['butir' => $instrumen, 'standarMutus' => $standarMutus]);
    }

    public function update(Request $request, ButirInstrumen $instrumen)
    {
        $instrumen->update($this->validated($request));

        return redirect()->route('admin.ami.instrumen.index', ['standar_mutu_id' => $instrumen->standar_mutu_id])
            ->with('success', 'Butir instrumen berhasil diperbarui.');
    }

    public function destroy(ButirInstrumen $instrumen)
    {
        $standarId = $instrumen->standar_mutu_id;
        $instrumen->delete();

        return redirect()->route('admin.ami.instrumen.index', ['standar_mutu_id' => $standarId])
            ->with('success', 'Butir instrumen berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'standar_mutu_id' => 'required|exists:standar_mutu,id',
            'kode' => 'nullable|string|max:20',
            'pertanyaan' => 'required|string',
            'indikator' => 'nullable|string',
            'bobot' => 'nullable|numeric|min:0|max:999.99',
            'target' => 'nullable|string|max:255',
            'jenis_bukti' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['bobot'] = $data['bobot'] ?? 1;
        $data['urutan'] = $data['urutan'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
