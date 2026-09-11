@extends('layouts.admin')

@section('title', 'Monev')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Monev</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">SPMI</li>
                    <li class="breadcrumb-item active">Monev</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.monev.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Monev
        </a>
    </div>

    <p class="text-muted mb-3">
        Monitoring dan evaluasi periodik proses akademik non-audit — kehadiran dosen, kesesuaian
        pelaksanaan dengan RPS, dan aspek operasional prodi lainnya.
    </p>

    <div class="row g-3 mb-3">
        @foreach([['Total', $stats['total'], 'primary'], ['Baik', $stats['baik'], 'success'], ['Cukup', $stats['cukup'], 'warning'], ['Kurang', $stats['kurang'], 'danger']] as [$l, $v, $c])
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
                <div class="col-md-3">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="baik" {{ request('status') === 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="cukup" {{ request('status') === 'cukup' ? 'selected' : '' }}>Cukup</option>
                        <option value="kurang" {{ request('status') === 'kurang' ? 'selected' : '' }}>Kurang</option>
                    </select>
                </div>
                <div class="col-md-3">
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
                            <th>Aspek Monev</th>
                            <th width="160">Prodi / Periode</th>
                            <th width="120">Tanggal</th>
                            <th width="120">Status</th>
                            <th width="130">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monev as $m)
                        <tr>
                            <td>
                                {{ $m->aspek_monev }}
                                <div class="small text-muted">{{ Str::limit($m->hasil, 80) }}</div>
                            </td>
                            <td><small>{{ $m->prodi->nama ?? 'Institusional' }}</small><div class="small text-muted">{{ $m->tahun_akademik }} - {{ ucfirst($m->semester) }}</div></td>
                            <td><small>{{ $m->tanggal_monev->format('d M Y') }}</small></td>
                            <td><span class="badge bg-{{ $m->status_color }}">{{ $m->status_label }}</span></td>
                            <td>
                                <a href="{{ route('admin.ami.monev.edit', $m) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.ami.monev.destroy', $m) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data monev.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($monev->hasPages())<div class="card-footer">{{ $monev->links() }}</div>@endif
    </div>
@endsection
