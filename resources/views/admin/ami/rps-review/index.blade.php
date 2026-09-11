@extends('layouts.admin')

@section('title', 'Review RPS')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Review RPS</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">SPMI</li>
                    <li class="breadcrumb-item active">Review RPS</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.rps-review.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah RPS
        </a>
    </div>

    <p class="text-muted mb-3">
        Rekam dan tinjau kesesuaian Rencana Pembelajaran Semester (RPS) tiap mata kuliah — kelengkapan CPMK,
        rencana asesmen, dan kesesuaian format — sebagai bagian dari evaluasi mutu pembelajaran.
    </p>

    <div class="row g-3 mb-3">
        @foreach([['Total RPS', $stats['total'], 'primary'], ['Sesuai', $stats['sesuai'], 'success'], ['Perlu Revisi', $stats['perlu_revisi'], 'danger'], ['Belum Direview', $stats['belum'], 'secondary']] as [$l, $v, $c])
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
                    <label class="form-label small mb-1">Unit / Prodi</label>
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
                        <option value="belum_direview" {{ request('status') === 'belum_direview' ? 'selected' : '' }}>Belum Direview</option>
                        <option value="sesuai" {{ request('status') === 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                        <option value="perlu_revisi" {{ request('status') === 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
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
                            <th width="70" class="text-center">SKS</th>
                            <th width="140">Semester / TA</th>
                            <th width="90">Berkas</th>
                            <th width="130">Status</th>
                            <th width="170">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rps as $r)
                        <tr>
                            <td>
                                {{ $r->mata_kuliah }}
                                <div class="small text-muted">{{ $r->kode_mk }} &middot; {{ optional($r->prodi)->nama }}</div>
                            </td>
                            <td>{{ $r->dosen_pengampu }}</td>
                            <td class="text-center">{{ $r->sks ?? '-' }}</td>
                            <td><small>{{ ucfirst($r->semester) }} {{ $r->tahun_akademik }}</small></td>
                            <td>
                                @if($r->file_path)
                                <a href="{{ Storage::url($r->file_path) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-pdf"></i></a>
                                @else
                                <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $r->status_color }}">{{ $r->status_label }}</span></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $r->id }}">
                                    <i class="bi bi-clipboard-check"></i> Review
                                </button>
                                <a href="{{ route('admin.ami.rps-review.edit', $r) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.ami.rps-review.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data RPS.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($rps->hasPages())<div class="card-footer">{{ $rps->links() }}</div>@endif
    </div>

    {{-- Review modals, one per row (kept outside the table so the markup stays valid HTML) --}}
    @foreach($rps as $r)
    <div class="modal fade" id="reviewModal{{ $r->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.ami.rps-review.review', $r) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title">Review RPS — {{ $r->mata_kuliah }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($r->reviewed_by)
                        <p class="small text-muted">Terakhir direview oleh {{ optional($r->reviewer)->name }}, {{ optional($r->reviewed_at)->format('d M Y H:i') }}.</p>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="belum_direview" {{ $r->status === 'belum_direview' ? 'selected' : '' }}>Belum Direview</option>
                                <option value="sesuai" {{ $r->status === 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                <option value="perlu_revisi" {{ $r->status === 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Catatan Reviewer</label>
                            <textarea name="catatan_reviewer" rows="3" class="form-control" placeholder="Wajib diisi jika status Perlu Revisi">{{ $r->catatan_reviewer }}</textarea>
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
