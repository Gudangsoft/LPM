<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\SurveyKepuasan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class SurveyKepuasanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:survey.view', only: ['index', 'show']),
            new Middleware('permission:survey.manage', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = SurveyKepuasan::with('prodi')
            ->when($request->filled('jenis_responden'), fn ($q) => $q->where('jenis_responden', $request->jenis_responden))
            ->when($request->filled('tahun_akademik'), fn ($q) => $q->where('tahun_akademik', $request->tahun_akademik))
            ->orderByDesc('tahun_akademik');

        $survey = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => (clone $query)->count(),
            'total_responden' => (clone $query)->sum('jumlah_responden'),
            'rata_rata' => round((clone $query)->whereNotNull('rata_rata_skor')->avg('rata_rata_skor'), 2) ?: 0,
        ];

        $prodiOptions = Prodi::orderBy('nama')->get();
        $tahunOptions = SurveyKepuasan::query()->distinct()->orderByDesc('tahun_akademik')->pluck('tahun_akademik');

        return view('admin.ami.survey-kepuasan.index', compact('survey', 'stats', 'prodiOptions', 'tahunOptions'));
    }

    public function create()
    {
        $prodiOptions = Prodi::orderBy('nama')->get();

        return view('admin.ami.survey-kepuasan.create', compact('prodiOptions'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $validated['file_path'] = $file->store('survey-kepuasan', 'public');
            $validated['file_name'] = $file->getClientOriginalName();
        }

        SurveyKepuasan::create($validated);

        return redirect()->route('admin.ami.survey-kepuasan.index')->with('success', 'Hasil survei berhasil ditambahkan.');
    }

    public function edit(SurveyKepuasan $surveyKepuasan)
    {
        $prodiOptions = Prodi::orderBy('nama')->get();

        return view('admin.ami.survey-kepuasan.edit', compact('surveyKepuasan', 'prodiOptions'));
    }

    public function update(Request $request, SurveyKepuasan $surveyKepuasan)
    {
        $validated = $this->validated($request);

        if ($request->hasFile('file')) {
            if ($surveyKepuasan->file_path) {
                Storage::disk('public')->delete($surveyKepuasan->file_path);
            }
            $file = $request->file('file');
            $validated['file_path'] = $file->store('survey-kepuasan', 'public');
            $validated['file_name'] = $file->getClientOriginalName();
        }

        $surveyKepuasan->update($validated);

        return redirect()->route('admin.ami.survey-kepuasan.index')->with('success', 'Hasil survei berhasil diperbarui.');
    }

    public function destroy(SurveyKepuasan $surveyKepuasan)
    {
        $surveyKepuasan->delete();

        return redirect()->route('admin.ami.survey-kepuasan.index')->with('success', 'Data survei dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'prodi_id' => 'nullable|exists:prodi,id',
            'jenis_responden' => 'required|in:mahasiswa,dosen,tendik,alumni,pengguna_lulusan',
            'judul_survei' => 'required|string|max:255',
            'tahun_akademik' => 'required|string|max:20',
            'semester' => 'nullable|in:ganjil,genap',
            'jumlah_responden' => 'required|integer|min:0',
            'rata_rata_skor' => 'nullable|numeric|min:0|max:100',
            'skala_maksimal' => 'required|numeric|min:1|max:100',
            'ringkasan_hasil' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);
    }
}
