@extends('layouts.admin')

@section('title', 'Benchmarking')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Benchmarking</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">SPMI</li>
                    <li class="breadcrumb-item active">Benchmarking</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.benchmarking.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Data
        </a>
    </div>

    <p class="text-muted mb-3">
        Perbandingan capaian mutu institusi/prodi dengan institusi lain sebagai bahan penetapan
        target peningkatan pada tahap Peningkatan siklus PPEPP.
    </p>

    <div class="row g-3 mb-3">
        @foreach([['Total Data', $stats['total'], 'primary'], ['Institusi Pembanding', $stats['institusi'], 'info'], ['Level Prodi', $stats['prodi'], 'secondary']] as [$l, $v, $c])
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
                        @foreach($prodiOptions as $p)
                        <option value="{{ $p->id }}" {{ request('prodi_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Tahun Akademik</label>
                    <select name="tahun_akademik" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($tahunOptions as $t)
                        <option value="{{ $t }}" {{ request('tahun_akademik') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Cari Aspek</label>
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
                            <th>Aspek</th>
                            <th width="160">Prodi / Tahun</th>
                            <th width="180">Institusi Pembanding</th>
                            <th width="110">Sendiri</th>
                            <th width="110">Pembanding</th>
                            <th width="130">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($benchmarking as $b)
                        <tr>
                            <td>
                                {{ $b->aspek }}
                                @if($b->standarMutu)<div class="small text-muted">{{ $b->standarMutu->nama }}</div>@endif
                            </td>
                            <td><small>{{ $b->prodi->nama ?? 'Institusional' }}</small><div class="small text-muted">{{ $b->tahun_akademik }}</div></td>
                            <td><small>{{ $b->institusi_pembanding }}</small></td>
                            <td><small>{{ $b->nilai_sendiri ?? '-' }}</small></td>
                            <td><small>{{ $b->nilai_pembanding ?? '-' }}</small></td>
                            <td>
                                <a href="{{ route('admin.ami.benchmarking.edit', $b) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.ami.benchmarking.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data benchmarking.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($benchmarking->hasPages())<div class="card-footer">{{ $benchmarking->links() }}</div>@endif
    </div>
@endsection
