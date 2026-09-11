@extends('layouts.admin')

@section('title', 'Survey Kepuasan')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Survey Kepuasan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">SPMI</li>
                    <li class="breadcrumb-item active">Survey Kepuasan</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.survey-kepuasan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Hasil Survei
        </a>
    </div>

    <p class="text-muted mb-3">
        Rekap hasil survei kepuasan mahasiswa, dosen, tenaga kependidikan, alumni, dan pengguna
        lulusan per periode. Survei dikumpulkan di luar sistem (mis. Google Form), hasilnya
        direkap dan dilampirkan di sini.
    </p>

    <div class="row g-3 mb-3">
        @foreach([['Jumlah Survei', $stats['total'], 'primary'], ['Total Responden', $stats['total_responden'], 'info'], ['Rata-rata Skor', $stats['rata_rata'], 'success']] as [$l, $v, $c])
        <div class="col-6 col-md-4">
            <div class="card border-{{ $c }}"><div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-{{ $c }}">{{ $v }}</div><small class="text-muted">{{ $l }}</small>
            </div></div>
        </div>
        @endforeach
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Jenis Responden</label>
                    <select name="jenis_responden" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach(['mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen', 'tendik' => 'Tenaga Kependidikan', 'alumni' => 'Alumni', 'pengguna_lulusan' => 'Pengguna Lulusan'] as $val => $label)
                        <option value="{{ $val }}" {{ request('jenis_responden') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Tahun Akademik</label>
                    <select name="tahun_akademik" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($tahunOptions as $t)
                        <option value="{{ $t }}" {{ request('tahun_akademik') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Survei</th>
                            <th width="140">Responden</th>
                            <th width="100" class="text-center">Jumlah</th>
                            <th width="150">Skor</th>
                            <th width="130">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($survey as $s)
                        <tr>
                            <td>
                                {{ $s->judul_survei }}
                                <div class="small text-muted">{{ $s->prodi->nama ?? 'Institusional' }} &middot; {{ $s->tahun_akademik }}{{ $s->semester ? ' - '.ucfirst($s->semester) : '' }}</div>
                            </td>
                            <td><small>{{ $s->jenis_responden_label }}</small></td>
                            <td class="text-center">{{ $s->jumlah_responden }}</td>
                            <td>
                                @if($s->rata_rata_skor !== null)
                                <span class="badge bg-success">{{ $s->rata_rata_skor }} / {{ $s->skala_maksimal }}</span>
                                <div class="small text-muted">{{ $s->persentase_skor }}%</div>
                                @else
                                <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.ami.survey-kepuasan.edit', $s) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.ami.survey-kepuasan.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada hasil survei.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($survey->hasPages())<div class="card-footer">{{ $survey->links() }}</div>@endif
    </div>
@endsection
