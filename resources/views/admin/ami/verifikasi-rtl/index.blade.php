@extends('layouts.admin')

@section('title', 'Verifikasi RTL')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Verifikasi Rencana Tindak Lanjut</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">SPMI</li>
                <li class="breadcrumb-item active">Verifikasi RTL</li>
            </ol>
        </nav>
    </div>

    <div class="row g-3 mb-3">
        @foreach([['Menunggu Verifikasi', $stats['menunggu'], 'warning'], ['Diterima', $stats['disetujui'], 'success'], ['Perlu Revisi', $stats['revisi'], 'danger']] as [$l, $v, $c])
        <div class="col-md-4">
            <div class="card border-{{ $c }}"><div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-{{ $c }}">{{ $v }}</div><small class="text-muted">{{ $l }}</small>
            </div></div>
        </div>
        @endforeach
    </div>

    <form method="GET" class="card card-body mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Unit / Prodi</label>
                <select name="prodi_id" class="form-select form-select-sm">
                    <option value="">Semua Unit</option>
                    @foreach($prodis as $p)<option value="{{ $p->id }}" {{ request('prodi_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filter</button></div>
        </div>
    </form>

    <h6 class="fw-semibold mb-2">Menunggu Verifikasi</h6>
    @forelse($menunggu as $tl)
    <div class="card mb-2">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2">
                <div>
                    <span class="badge bg-secondary">{{ optional(optional($tl->temuanAmi->jadwalAmi)->prodi)->nama ?? '-' }}</span>
                    <span class="text-muted small">Standar: {{ optional($tl->temuanAmi->standarMutu)->nama ?? optional($tl->temuanAmi)->standar }}</span>
                </div>
                <a href="{{ route('admin.ami.temuan.show', $tl->temuanAmi) }}" class="small">Lihat temuan &raquo;</a>
            </div>
            <div class="mt-2"><strong>Temuan:</strong> {{ \Illuminate\Support\Str::limit(optional($tl->temuanAmi)->deskripsi, 160) }}</div>
            <div class="mt-1"><strong>RTL (oleh {{ optional($tl->user)->name }}):</strong> {{ $tl->deskripsi }}</div>
            @if($tl->file_bukti)
            <div class="mt-1"><a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($tl->file_bukti) }}" target="_blank" rel="noopener"><i class="bi bi-paperclip"></i> Bukti</a></div>
            @endif

            <form action="{{ route('admin.ami.verifikasi-rtl.verify', $tl) }}" method="POST" class="row g-2 mt-2 border-top pt-2">
                @csrf
                <div class="col-md-3">
                    <select name="aksi" class="form-select form-select-sm" required>
                        <option value="">— aksi —</option>
                        <option value="terima">Terima (tutup temuan)</option>
                        <option value="revisi">Minta Revisi</option>
                    </select>
                </div>
                <div class="col-md-7"><input name="catatan_reviewer" class="form-control form-control-sm" placeholder="Catatan verifikasi (wajib untuk revisi)"></div>
                <div class="col-md-2"><button class="btn btn-sm btn-primary w-100">Proses</button></div>
            </form>
        </div>
    </div>
    @empty
    <div class="alert alert-success">Tidak ada RTL yang menunggu verifikasi. 🎉</div>
    @endforelse
    @if($menunggu->hasPages())<div class="mb-3">{{ $menunggu->links() }}</div>@endif

    @if($riwayat->isNotEmpty())
    <h6 class="fw-semibold mb-2 mt-4">Riwayat Terakhir</h6>
    <div class="card"><div class="card-body p-0"><div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Unit</th><th>RTL</th><th width="110">Hasil</th><th width="140">Oleh</th><th width="120">Tanggal</th></tr></thead>
            <tbody>
                @foreach($riwayat as $r)
                <tr>
                    <td><small>{{ optional(optional(optional($r->temuanAmi)->jadwalAmi)->prodi)->nama ?? '-' }}</small></td>
                    <td><small>{{ \Illuminate\Support\Str::limit($r->deskripsi, 80) }}</small></td>
                    <td><span class="badge bg-{{ $r->status === 'approved' ? 'success' : 'danger' }}">{{ $r->status === 'approved' ? 'Diterima' : 'Revisi' }}</span></td>
                    <td><small>{{ optional($r->reviewer)->name ?? '-' }}</small></td>
                    <td><small>{{ optional($r->reviewed_at)->format('d M Y') }}</small></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div></div></div>
    @endif
@endsection
