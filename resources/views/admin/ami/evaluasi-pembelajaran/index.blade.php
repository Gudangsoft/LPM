@extends('layouts.admin')

@section('title', 'Evaluasi Pembelajaran')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Evaluasi Pembelajaran</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">SPMI</li>
                    <li class="breadcrumb-item active">Evaluasi Pembelajaran</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.evaluasi-pembelajaran.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Mata Kuliah
        </a>
    </div>

    <p class="text-muted mb-3">
        Evaluasi pelaksanaan pembelajaran tiap mata kuliah — kesesuaian dengan RPS, kendala yang
        dihadapi, dan rekomendasi perbaikan untuk semester berikutnya.
    </p>

    <div class="row g-3 mb-3">
        @foreach([['Total Mata Kuliah', $stats['total'], 'primary'], ['Sudah Dievaluasi', $stats['dievaluasi'], 'success'], ['Belum Dievaluasi', $stats['belum'], 'secondary']] as [$l, $v, $c])
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
                <div class="col-md-3">
                    <label class="form-label small mb-1">Prodi</label>
                    <select name="prodi_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ request('prodi_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Tahun Akademik</label>
                    <select name="tahun_akademik" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($tahunOptions as $t)
                        <option value="{{ $t }}" {{ request('tahun_akademik') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="belum_dievaluasi" {{ request('status') === 'belum_dievaluasi' ? 'selected' : '' }}>Belum</option>
                        <option value="dievaluasi" {{ request('status') === 'dievaluasi' ? 'selected' : '' }}>Sudah</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Mata kuliah / dosen...">
                </div>
                <div class="col-md-2">
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
                            <th>Mata Kuliah</th>
                            <th>Dosen Pengampu</th>
                            <th width="140">Semester / TA</th>
                            <th width="140">Kesesuaian RPS</th>
                            <th width="130">Status</th>
                            <th width="170">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluasi as $e)
                        <tr>
                            <td>{{ $e->mata_kuliah }}<div class="small text-muted">{{ optional($e->prodi)->nama }}</div></td>
                            <td>{{ $e->dosen_pengampu }}</td>
                            <td><small>{{ ucfirst($e->semester) }} {{ $e->tahun_akademik }}</small></td>
                            <td><small>{{ $e->kesesuaian_rps_label }}</small></td>
                            <td><span class="badge bg-{{ $e->status_color }}">{{ $e->status === 'dievaluasi' ? 'Dievaluasi' : 'Belum' }}</span></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#evalModal{{ $e->id }}">
                                    <i class="bi bi-clipboard-check"></i> Evaluasi
                                </button>
                                <a href="{{ route('admin.ami.evaluasi-pembelajaran.edit', $e) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.ami.evaluasi-pembelajaran.destroy', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data evaluasi pembelajaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($evaluasi->hasPages())<div class="card-footer">{{ $evaluasi->links() }}</div>@endif
    </div>

    {{-- Evaluation modals, one per row (kept outside the table so the markup stays valid HTML) --}}
    @foreach($evaluasi as $e)
    <div class="modal fade" id="evalModal{{ $e->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.ami.evaluasi-pembelajaran.evaluasi', $e) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title">Evaluasi Pembelajaran — {{ $e->mata_kuliah }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($e->dievaluasi_oleh)
                        <p class="small text-muted">Terakhir dievaluasi oleh {{ optional($e->evaluator)->name }}, {{ optional($e->dievaluasi_pada)->format('d M Y H:i') }}.</p>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Kesesuaian dengan RPS</label>
                            <select name="kesesuaian_rps" class="form-select" required>
                                <option value="sesuai" {{ $e->kesesuaian_rps === 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                <option value="kurang_sesuai" {{ $e->kesesuaian_rps === 'kurang_sesuai' ? 'selected' : '' }}>Kurang Sesuai</option>
                                <option value="tidak_sesuai" {{ $e->kesesuaian_rps === 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kendala</label>
                            <textarea name="kendala" rows="2" class="form-control">{{ $e->kendala }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Rekomendasi</label>
                            <textarea name="rekomendasi" rows="2" class="form-control">{{ $e->rekomendasi }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endsection
