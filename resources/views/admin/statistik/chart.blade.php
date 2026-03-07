@extends('layouts.admin')

@section('title', 'Grafik Statistik')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Grafik Statistik {{ $kategoriOptions[$kategori] ?? '' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.statistik.index') }}">Statistik</a></li>
                    <li class="breadcrumb-item active">Grafik</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.statistik.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.statistik.chart') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select">
                        @foreach($kategoriOptions as $key => $label)
                        <option value="{{ $key }}" {{ $kategori == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dari Tahun</label>
                    <input type="number" name="from_year" class="form-control" value="{{ $fromYear }}" min="2000" max="{{ now()->year }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai Tahun</label>
                    <input type="number" name="to_year" class="form-control" value="{{ $toYear }}" min="2000" max="{{ now()->year + 5 }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card bg-indigo">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ number_format($summary['total_usulan']) }}</div>
                        <div class="stat-label">Total Usulan</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-purple">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ number_format($summary['total_didanai']) }}</div>
                        <div class="stat-label">Total Didanai</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $summary['persentase_didanai'] }}%</div>
                        <div class="stat-label">Persentase Diterima</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-percent"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-cyan">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">Rp {{ number_format($summary['total_dana_disetujui']/1000000, 0) }}jt</div>
                        <div class="stat-label">Total Dana</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-graph-up me-2"></i>Tren {{ $kategoriOptions[$kategori] ?? '' }} ({{ $fromYear }} - {{ $toYear }})
            </h5>
        </div>
        <div class="card-body">
            <canvas id="statistikChart" height="100"></canvas>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Data per Tahun</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tahun</th>
                            <th class="text-center">Usulan</th>
                            <th class="text-center">Didanai</th>
                            <th class="text-center">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 0; $i < count($chartData['labels']); $i++)
                        @php
                            $usulan = $chartData['datasets'][0]['data'][$i];
                            $didanai = $chartData['datasets'][1]['data'][$i];
                            $persen = $usulan > 0 ? round(($didanai / $usulan) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td><strong>{{ $chartData['labels'][$i] }}</strong></td>
                            <td class="text-center">{{ $usulan }}</td>
                            <td class="text-center">{{ $didanai }}</td>
                            <td class="text-center">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" style="width: {{ $persen }}%">
                                        {{ $persen }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('statistikChart').getContext('2d');
    const chartData = @json($chartData);
    
    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 13,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    padding: 12,
                    cornerRadius: 8,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 12 }
                    },
                    grid: {
                        color: 'rgba(139, 92, 246, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: { size: 12 }
                    }
                }
            },
            elements: {
                point: {
                    radius: 5,
                    hoverRadius: 8,
                    borderWidth: 3,
                    backgroundColor: 'white'
                },
                line: {
                    borderWidth: 3
                }
            }
        }
    });
</script>
@endpush
