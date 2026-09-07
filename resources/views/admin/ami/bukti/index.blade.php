@extends('layouts.admin')

@section('title', 'Dokumen / Bukti Audit')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Dokumen / Bukti Audit</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">SPMI</li>
                <li class="breadcrumb-item active">Dokumen / Bukti</li>
            </ol>
        </nav>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Periode</label>
                    <select name="periode_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($periodes as $p)
                        <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Unit / Prodi</label>
                    <select name="prodi_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($prodis as $pr)
                        <option value="{{ $pr->id }}" {{ request('prodi_id') == $pr->id ? 'selected' : '' }}>{{ $pr->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Status Validasi</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum</option>
                        <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>Valid</option>
                        <option value="tidak_valid" {{ request('status') === 'tidak_valid' ? 'selected' : '' }}>Tidak Valid</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                    <a href="{{ route('admin.ami.bukti.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                            <th>Bukti</th>
                            <th>Unit</th>
                            <th>Standar / Butir</th>
                            <th width="70">Versi</th>
                            <th width="100">Validasi</th>
                            <th width="140">Diunggah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bukti as $bk)
                        <tr>
                            <td>
                                @if($bk->url)<a href="{{ $bk->url }}" target="_blank" rel="noopener">{{ $bk->judul }}</a>@else{{ $bk->judul }}@endif
                                @if($bk->keterangan)<div class="small text-muted">{{ \Illuminate\Support\Str::limit($bk->keterangan, 80) }}</div>@endif
                            </td>
                            <td><small>{{ optional(optional($bk->auditButir->jadwalAmi)->prodi)->nama ?? '-' }}</small></td>
                            <td><small>{{ optional(optional($bk->auditButir->butir)->standarMutu)->nama ?? '-' }} — {{ \Illuminate\Support\Str::limit(optional($bk->auditButir->butir)->pertanyaan, 60) }}</small></td>
                            <td>v{{ $bk->versi }}</td>
                            <td><span class="badge bg-{{ $bk->status_color }}">{{ $bk->status_validasi }}</span></td>
                            <td><small>{{ optional($bk->uploader)->name ?? '-' }}<br>{{ $bk->created_at->format('d M Y') }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada bukti audit.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($bukti->hasPages())
        <div class="card-footer">{{ $bukti->links() }}</div>
        @endif
    </div>
@endsection
