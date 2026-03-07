@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="date-badge">
            <i class="bi bi-calendar3"></i>
            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y') }}
        </div>
        <h1 class="welcome-title">{{ __('admin.welcome') }}, {{ auth()->user()->name }}!</h1>
        <p class="welcome-subtitle">
            @php
                $hour = now()->hour;
                $dayName = \Carbon\Carbon::now()->locale('id')->translatedFormat('l');
                if ($hour < 12) {
                    $greeting = __('admin.good_morning');
                } elseif ($hour < 17) {
                    $greeting = __('admin.good_afternoon');
                } else {
                    $greeting = __('admin.good_evening');
                }
            @endphp
            {{ $greeting }}! {{ __('admin.have_nice_day', ['day' => $dayName]) }}
        </p>
        <p class="welcome-period">
            <strong>{{ __('admin.current_period') }}:</strong> 
            {{ $siteSettings['current_period'] ?? __('admin.no_active_period') }}
        </p>
        
        <div class="sparkles">
            <span class="sparkle">✦</span>
            <span class="sparkle">✦</span>
            <span class="sparkle">✦</span>
        </div>
        
        <img src="{{ asset('images/welcome-character.svg') }}" alt="Welcome" class="welcome-illustration" onerror="this.style.display='none'">
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-blue">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $stats['berita'] }}</div>
                        <div class="stat-label">{{ __('admin.total_news') }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-newspaper"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $stats['dokumen'] }}</div>
                        <div class="stat-label">{{ __('admin.total_documents') }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-purple">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $stats['galeri'] }}</div>
                        <div class="stat-label">{{ __('admin.total_gallery') }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-images"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-orange">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $stats['users'] }}</div>
                        <div class="stat-label">{{ __('admin.total_users') }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitor Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="stat-card bg-cyan">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $stats['visitors_today'] }}</div>
                        <div class="stat-label">{{ __('admin.visitors_today') }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-eye"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="stat-card bg-red">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $stats['visitors_month'] }}</div>
                        <div class="stat-label">{{ __('admin.visitors_month') }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="stat-card bg-green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $stats['kontak_unread'] }}</div>
                        <div class="stat-label">{{ __('admin.unread_messages') }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Latest News -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-newspaper me-2"></i>{{ __('admin.latest_news') }}</span>
                    <a href="{{ route('admin.berita.index') }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view_all') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @forelse($latestBerita as $berita)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ Str::limit($berita->judul, 40) }}</div>
                                        <small class="text-muted">{{ $berita->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-end">
                                        @if($berita->is_published)
                                        <span class="badge bg-success">{{ __('admin.published') }}</span>
                                        @else
                                        <span class="badge bg-warning">{{ __('admin.draft') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Messages -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-envelope me-2"></i>{{ __('admin.latest_messages') }}</span>
                    <a href="{{ route('admin.kontak.index') }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view_all') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @forelse($latestKontak as $kontak)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $kontak->nama }}</div>
                                        <small class="text-muted">{{ Str::limit($kontak->subjek, 30) }}</small>
                                    </td>
                                    <td class="text-end">
                                        @if($kontak->status == 'unread')
                                        <span class="badge bg-danger">{{ __('admin.unread') }}</span>
                                        @elseif($kontak->status == 'read')
                                        <span class="badge bg-warning">{{ __('admin.read') }}</span>
                                        @else
                                        <span class="badge bg-success">{{ __('admin.replied') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitor Chart -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-bar-chart me-2"></i>{{ __('admin.visitor_statistics') }}
        </div>
        <div class="card-body">
            <canvas id="visitorChart" height="100"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('visitorChart').getContext('2d');
    const visitorData = @json($visitorStats);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: visitorData.map(item => item.date),
            datasets: [{
                label: '{{ __("admin.visitors") }}',
                data: visitorData.map(item => item.total),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
