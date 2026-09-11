@extends('layouts.admin')

@section('title', 'Capaian Pembelajaran')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Capaian Pembelajaran</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">SPMI</li>
                    <li class="breadcrumb-item active">Capaian Pembelajaran</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.capaian-pembelajaran.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah CPMK
        </a>
    </div>

    <p class="text-muted mb-3">
        Target dan realisasi capaian pembelajaran mata kuliah (CPMK) per tahun akademik. Status
        tercapai/tidak tercapai dihitung otomatis dari perbandingan realisasi terhadap target.
    </p>

    <div class="row g-3 mb-3">
        @foreach([['Total CPMK', $stats['total'], 'primary'], ['Tercapai', $stats['tercapai'], 'success'], ['Tidak Tercapai', $stats['tidak_tercapai'], 'danger'], ['Belum Dievaluasi', $stats['belum'], 'secondary']] as [$l, $v, $c])
        <div class="col-6 col-md-3">
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
                        <option value="belum_dievaluasi" {{ request('status') === 'belum_dievaluasi' ? 'selected' : '' }}>Belum Dievaluasi</option>
                        <option value="tercapai" {{ request('status') === 'tercapai' ? 'selected' : '' }}>Tercapai</option>
                        <option value="tidak_tercapai" {{ request('status') === 'tidak_tercapai' ? 'selected' : '' }}>Tidak Tercapai</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Cari Mata Kuliah</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm">
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
                            <th>Mata Kuliah / CPMK</th>
                            <th width="150">Prodi / Periode</th>
                            <th width="100" class="text-center">Target</th>
                            <th width="100" class="text-center">Realisasi</th>
                            <th width="140">Status</th>
                            <th width="130">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($capaian as $c)
                        <tr>
                            <td>{{ $c->mata_kuliah }}<div class="small text-muted">{{ $c->cpmk }}</div></td>
                            <td><small>{{ optional($c->prodi)->nama }}</small><div class="small text-muted">{{ $c->tahun_akademik }} - {{ ucfirst($c->semester) }}</div></td>
                            <td class="text-center">{{ $c->target_capaian }}%</td>
                            <td class="text-center">{{ $c->realisasi_capaian !== null ? $c->realisasi_capaian.'%' : '-' }}</td>
                            <td><span class="badge bg-{{ $c->status_color }}">{{ $c->status_label }}</span></td>
                            <td>
                                <a href="{{ route('admin.ami.capaian-pembelajaran.edit', $c) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.ami.capaian-pembelajaran.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data capaian pembelajaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($capaian->hasPages())<div class="card-footer">{{ $capaian->links() }}</div>@endif
    </div>
@endsection
