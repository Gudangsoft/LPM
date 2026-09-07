@extends('layouts.admin')

@section('title', 'Dashboard AMI')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Dashboard Audit Mutu Internal</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">SPMI</li>
                    <li class="breadcrumb-item active">Dashboard AMI</li>
                </ol>
            </nav>
        </div>
        <form method="GET" class="d-flex align-items-center gap-2">
            <label class="form-label mb-0 small text-muted">Periode</label>
            <select name="periode_id" class="form-select form-select-sm" style="min-width: 220px" onchange="this.form.submit()">
                @foreach($periodes as $p)
                <option value="{{ $p->id }}" {{ $periode && $periode->id === $p->id ? 'selected' : '' }}>
                    {{ $p->nama }}{{ $p->status === 'aktif' ? ' (aktif)' : '' }}
                </option>
                @endforeach
            </select>
        </form>
    </div>

    @if(!$periode)
        <div class="alert alert-info">Belum ada periode AMI. Buat periode terlebih dahulu di menu Periode AMI.</div>
    @else
    <div class="row g-3 mb-4">
        @php
            $tiles = [
                ['Periode', $cards['periode'], 'bi-calendar3', 'primary'],
                ['Unit Diaudit', $cards['unit_diaudit'], 'bi-mortarboard', 'info'],
                ['Auditor Aktif', $cards['auditor'], 'bi-person-badge', 'secondary'],
                ['Total Temuan', $cards['temuan_total'], 'bi-exclamation-diamond', 'warning'],
                ['Temuan Selesai', $cards['temuan_selesai'], 'bi-check-circle', 'success'],
                ['Belum Selesai', $cards['temuan_belum'], 'bi-hourglass-split', 'danger'],
                ['Penyelesaian RTL', $cards['rtl_persen'].'%', 'bi-clipboard-check', 'primary'],
            ];
        @endphp
        @foreach($tiles as [$label, $value, $icon, $color])
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-{{ $color }} bg-opacity-10 text-{{ $color }}" style="width:44px;height:44px;flex:none">
                        <i class="bi {{ $icon }} fs-5"></i>
                    </span>
                    <div class="min-w-0">
                        <div class="fw-bold text-truncate" style="font-size:1.15rem">{{ $value }}</div>
                        <small class="text-muted">{{ $label }}</small>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100"><div class="card-header">Progres Audit (Jadwal per Status)</div>
                <div class="card-body"><canvas id="chJadwal" height="150"></canvas></div></div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100"><div class="card-header">Status Temuan</div>
                <div class="card-body"><canvas id="chTemuan" height="150"></canvas></div></div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100"><div class="card-header">Temuan per Kategori</div>
                <div class="card-body"><canvas id="chKategori" height="150"></canvas></div></div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100"><div class="card-header">Temuan per Unit (10 teratas)</div>
                <div class="card-body"><canvas id="chUnit" height="150"></canvas></div></div>
        </div>
        <div class="col-12">
            <div class="card"><div class="card-header">Temuan per Standar</div>
                <div class="card-body"><canvas id="chStandar" height="90"></canvas></div></div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    const palette = ['#3b82f6', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#0ea5e9', '#f43f5e', '#22c55e', '#eab308', '#64748b'];
    const mk = (id, type, obj, label) => {
        const el = document.getElementById(id);
        if (!el) return;
        const labels = Object.keys(obj || {});
        const data = Object.values(obj || {});
        new Chart(el.getContext('2d'), {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label || '',
                    data: data,
                    backgroundColor: type === 'bar' ? '#3b82f6' : palette,
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: type !== 'bar', position: 'bottom' } },
                scales: type === 'bar' ? { y: { beginAtZero: true, ticks: { precision: 0 } } } : {}
            }
        });
    };
    mk('chJadwal', 'doughnut', @json($chart['jadwalStatus']));
    mk('chTemuan', 'doughnut', @json($chart['temuanStatus']));
    mk('chKategori', 'doughnut', @json($chart['temuanKategori']));
    mk('chUnit', 'bar', @json($chart['temuanPerUnit']), 'Temuan');
    mk('chStandar', 'bar', @json($chart['temuanPerStandar']), 'Temuan');
})();
</script>
@endpush
