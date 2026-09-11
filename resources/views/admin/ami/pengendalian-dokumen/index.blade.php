@extends('layouts.admin')

@section('title', 'Pengendalian Dokumen')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Pengendalian Dokumen</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">SPMI</li>
                <li class="breadcrumb-item active">Pengendalian Dokumen</li>
            </ol>
        </nav>
    </div>

    <p class="text-muted mb-3">
        Daftar induk seluruh dokumen SPMI lintas jenis beserta status kendali mutunya — versi
        berjalan, tanggal terakhir ditinjau, dan jadwal tinjauan berikutnya. Atur siklus tinjau
        (dalam bulan) per dokumen agar sistem bisa menandai dokumen yang sudah jatuh tempo.
    </p>

    <div class="row g-3 mb-3">
        @foreach([['Total Dokumen', $stats['total'], 'primary'], ['Disetujui', $stats['approved'], 'success'], ['Ada Periode Tinjau', $stats['ada_periode_tinjau'], 'info'], ['Jatuh Tempo Tinjau', $stats['overdue'], 'danger']] as [$l, $v, $c])
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
                <div class="col-md-4">
                    <label class="form-label small mb-1">Jenis Dokumen</label>
                    <select name="jenis" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($jenisDokumen as $jd)
                        <option value="{{ $jd->id }}" {{ request('jenis') == $jd->id ? 'selected' : '' }}>{{ $jd->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Menunggu Review</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Jatuh Tempo Tinjau</option>
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
                            <th>Dokumen</th>
                            <th width="130">Jenis</th>
                            <th width="80" class="text-center">Versi</th>
                            <th width="130">Status</th>
                            <th width="140">Terakhir Ditinjau</th>
                            <th width="220">Siklus Tinjau</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumens as $d)
                        <tr>
                            <td>
                                <a href="{{ route('admin.dokumen.edit', $d) }}">{{ $d->judul }}</a>
                            </td>
                            <td><span class="badge bg-secondary">{{ optional($d->jenisDokumen)->nama }}</span></td>
                            <td class="text-center">v{{ $d->current_version ?? 1 }}</td>
                            <td><span class="badge bg-{{ $d->status_color }}">{{ ucfirst($d->status) }}</span></td>
                            <td>
                                <small>{{ optional($d->reviewed_at)->format('d M Y') ?? '-' }}</small>
                                @if($d->is_review_overdue)
                                <div><span class="badge bg-danger">Jatuh tempo {{ $d->next_review_date->format('d M Y') }}</span></div>
                                @elseif($d->next_review_date)
                                <div><small class="text-muted">Tinjau lagi: {{ $d->next_review_date->format('d M Y') }}</small></div>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.ami.pengendalian-dokumen.update', $d) }}" method="POST" class="d-flex gap-1">
                                    @csrf @method('PATCH')
                                    <input type="number" name="periode_tinjau_bulan" min="1" max="120" class="form-control form-control-sm" style="max-width:90px" value="{{ $d->periode_tinjau_bulan }}" placeholder="bulan">
                                    <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-check-lg"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada dokumen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($dokumens->hasPages())<div class="card-footer">{{ $dokumens->links() }}</div>@endif
    </div>
@endsection
