@extends('layouts.admin')

@section('title', 'Analitik Mutu')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Analitik Mutu</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Analitik Mutu</li>
            </ol>
        </nav>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="card h-100"><div class="card-header">Tren Jumlah Temuan per Periode</div>
                <div class="card-body"><canvas id="chTemuanPeriode" height="150"></canvas></div></div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100"><div class="card-header">Tren Rata-rata Nilai Auditor per Periode</div>
                <div class="card-body"><canvas id="chNilaiPeriode" height="150"></canvas></div></div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Perbandingan Antar Unit</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Unit</th>
                            <th width="110" class="text-center">Temuan</th>
                            <th width="110" class="text-center">Selesai</th>
                            <th width="200">% Penyelesaian</th>
                            <th width="140" class="text-center">Rata Nilai Auditor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perUnit as $u)
                        <tr>
                            <td>{{ $u->unit }}</td>
                            <td class="text-center">{{ $u->temuan }}</td>
                            <td class="text-center text-success">{{ $u->selesai }}</td>
                            <td>
                                <div class="progress" style="height:16px">
                                    <div class="progress-bar bg-{{ $u->persen >= 80 ? 'success' : ($u->persen >= 40 ? 'warning' : 'danger') }}" style="width:{{ $u->persen }}%">{{ $u->persen }}%</div>
                                </div>
                            </td>
                            <td class="text-center">{{ $u->rata_nilai ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Temuan Berulang (muncul di &gt; 1 periode)</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Unit</th>
                            <th>Standar / Aspek</th>
                            <th width="120" class="text-center">Total Temuan</th>
                            <th width="120" class="text-center">Jumlah Periode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($temuanBerulang as $t)
                        <tr>
                            <td>{{ $t->unit }}</td>
                            <td>{{ $t->label }}</td>
                            <td class="text-center">{{ $t->total }}</td>
                            <td class="text-center"><span class="badge bg-warning text-dark">{{ $t->periode }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada temuan berulang. 🎉</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    const mkLine = (id, obj, label, color) => {
        const el = document.getElementById(id);
        if (!el) return;
        new Chart(el.getContext('2d'), {
            type: 'line',
            data: {
                labels: Object.keys(obj || {}),
                datasets: [{ label: label, data: Object.values(obj || {}), borderColor: color, backgroundColor: color + '22', fill: true, tension: 0.3 }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    };
    mkLine('chTemuanPeriode', @json($temuanPerPeriode), 'Temuan', '#ef4444');
    mkLine('chNilaiPeriode', @json($nilaiPerPeriode), 'Rata-rata nilai', '#3b82f6');
})();
</script>
@endpush
