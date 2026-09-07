@extends('layouts.admin')

@section('title', 'Monitoring AMI')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Monitoring Tindak Lanjut</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">SPMI</li>
                    <li class="breadcrumb-item active">Monitoring</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Periode</label>
                    <select name="periode_id" class="form-select form-select-sm">
                        @foreach($periodes as $p)
                        <option value="{{ $p->id }}" {{ $periode && $periode->id === $p->id ? 'selected' : '' }}>{{ $p->nama }}{{ $p->status === 'aktif' ? ' (aktif)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Unit / Prodi</label>
                    <select name="prodi_id" class="form-select form-select-sm">
                        <option value="">Semua Unit</option>
                        @foreach($prodis as $pr)
                        <option value="{{ $pr->id }}" {{ request('prodi_id') == $pr->id ? 'selected' : '' }}>{{ $pr->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel me-1"></i>Terapkan</button>
                    <a href="{{ route('admin.ami.monitoring') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @php
            $tiles = [
                ['Open', $cards['open'], 'danger'],
                ['In Progress', $cards['in_progress'], 'warning'],
                ['Telat (Overdue)', $cards['overdue'], 'danger'],
                ['Temuan Selesai', $cards['closed'], 'success'],
                ['RTL Diajukan', $cards['rtl_submitted'], 'info'],
                ['RTL Disetujui', $cards['rtl_approved'], 'success'],
                ['RTL Ditolak', $cards['rtl_rejected'], 'secondary'],
            ];
        @endphp
        @foreach($tiles as [$label, $value, $color])
        <div class="col-6 col-md-3">
            <div class="card border-{{ $color }} h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-3 fw-bold text-{{ $color }}">{{ $value }}</div>
                    <small class="text-muted">{{ $label }}</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card mb-4">
        <div class="card-header">Rekap Temuan per Unit</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Unit / Prodi</th>
                            <th width="90" class="text-center">Total</th>
                            <th width="90" class="text-center">Selesai</th>
                            <th width="90" class="text-center">Belum</th>
                            <th width="90" class="text-center">Telat</th>
                            <th width="200">Penyelesaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rollup as $r)
                        @php $pct = $r->total > 0 ? round($r->selesai / $r->total * 100) : 0; @endphp
                        <tr>
                            <td>{{ $r->nama }}</td>
                            <td class="text-center">{{ $r->total }}</td>
                            <td class="text-center text-success">{{ $r->selesai }}</td>
                            <td class="text-center text-danger">{{ $r->belum }}</td>
                            <td class="text-center">{{ $r->telat > 0 ? '⚠ '.$r->telat : '0' }}</td>
                            <td>
                                <div class="progress" style="height: 18px">
                                    <div class="progress-bar bg-{{ $pct >= 80 ? 'success' : ($pct >= 40 ? 'warning' : 'danger') }}" style="width: {{ $pct }}%">{{ $pct }}%</div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data temuan untuk filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Temuan Melewati Batas Tindak Lanjut</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Unit</th>
                            <th>Standar</th>
                            <th>Deskripsi</th>
                            <th width="120">Batas</th>
                            <th width="110">Telat</th>
                            <th width="90">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overdueList as $t)
                        <tr>
                            <td><small>{{ optional($t->jadwalAmi->prodi)->nama ?? '-' }}</small></td>
                            <td><small>{{ optional($t->standarMutu)->nama ?? $t->standar }}</small></td>
                            <td>{{ \Illuminate\Support\Str::limit($t->deskripsi, 90) }}</td>
                            <td>{{ optional($t->batas_tindak_lanjut)->format('d M Y') }}</td>
                            <td class="text-danger">{{ abs((int) $t->days_until_deadline) }} hari</td>
                            <td><span class="badge bg-{{ $t->status_color }}">{{ $t->status }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada temuan yang telat. 🎉</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($overdueList->hasPages())
        <div class="card-footer">{{ $overdueList->links() }}</div>
        @endif
    </div>
@endsection
