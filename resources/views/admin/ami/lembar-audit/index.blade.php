@extends('layouts.admin')

@section('title', 'Lembar Kerja Audit')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Lembar Kerja Audit</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">SPMI</li>
                <li class="breadcrumb-item active">Lembar Kerja Audit</li>
            </ol>
        </nav>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Periode</label>
                    <select name="periode_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Periode</option>
                        @foreach($periodes as $p)
                        <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                        @endforeach
                    </select>
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
                            <th>Unit / Prodi</th>
                            <th>Periode</th>
                            <th width="140">Tanggal Audit</th>
                            <th width="130">Evaluasi Diri</th>
                            <th width="150">Progres Penilaian</th>
                            <th width="120">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $j)
                        @php
                            $tot = $j->total_butir_count ?? 0;
                            $done = $j->terisi_auditor_count ?? 0;
                            $pct = $tot > 0 ? round($done / $tot * 100) : 0;
                        @endphp
                        <tr>
                            <td>{{ optional($j->prodi)->nama ?? '-' }}</td>
                            <td><small>{{ optional($j->periodeAmi)->nama ?? '-' }}</small></td>
                            <td><small>{{ optional($j->tanggal_audit)->format('d M Y') }}</small></td>
                            <td>
                                <span class="badge bg-{{ ($j->evaluasiDiri?->status ?? '') === 'submitted' ? 'success' : 'secondary' }}">
                                    {{ ($j->evaluasiDiri?->status ?? '') === 'submitted' ? 'Dikirim' : 'Belum' }}
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height:16px">
                                    <div class="progress-bar bg-{{ $pct >= 100 ? 'success' : 'info' }}" style="width:{{ $pct }}%">{{ $done }}/{{ $tot }}</div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.ami.lembar-audit.show', $j) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-clipboard-check me-1"></i>Nilai
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada jadwal audit untuk Anda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($jadwals->hasPages())
        <div class="card-footer">{{ $jadwals->links() }}</div>
        @endif
    </div>
@endsection
