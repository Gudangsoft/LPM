@extends('layouts.admin')

@section('title', 'Evaluasi Diri')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Evaluasi Diri (AMI)</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">SPMI</li>
                <li class="breadcrumb-item active">Evaluasi Diri</li>
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
                            <th width="140">Status Evaluasi</th>
                            <th width="120">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $j)
                        <tr>
                            <td>{{ optional($j->prodi)->nama ?? '-' }}</td>
                            <td><small>{{ optional($j->periodeAmi)->nama ?? '-' }}</small></td>
                            <td><small>{{ optional($j->tanggal_audit)->format('d M Y') }}</small></td>
                            <td>
                                @php $st = $j->evaluasiDiri?->status ?? 'draft'; @endphp
                                <span class="badge bg-{{ $st === 'submitted' ? 'success' : 'secondary' }}">
                                    {{ $st === 'submitted' ? 'Sudah Dikirim' : 'Draft' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.ami.evaluasi-diri.show', $j) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square me-1"></i>Isi
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada jadwal audit.</td></tr>
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
