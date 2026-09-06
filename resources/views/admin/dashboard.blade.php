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
        @if($myProdi)
        <p class="welcome-period">
            <strong>Program Studi:</strong> {{ $myProdi->full_name }}
        </p>
        @endif
    </div>

    @if(auth()->user()->isAdmin())
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
            <div class="stat-card bg-slate">
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
            <div class="stat-card bg-indigo">
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
            <div class="stat-card bg-purple">
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
            <div class="stat-card bg-red">
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
    @endif

    <!-- AMI / Akreditasi Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.akreditasi.dashboard') }}" class="text-decoration-none">
                <div class="stat-card bg-amber">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value">{{ $amiStats['akreditasi_expiring'] }}</div>
                            <div class="stat-label">{{ __('admin.accreditation_expiring') }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-award"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.ami.temuan.index', ['status' => 'open']) }}" class="text-decoration-none">
                <div class="stat-card bg-red">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value">{{ $amiStats['temuan_overdue'] }}</div>
                            <div class="stat-label">{{ __('admin.finding_overdue') }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.ami.tindak-lanjut.pending') }}" class="text-decoration-none">
                <div class="stat-card bg-teal">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value">{{ $amiStats['tindak_lanjut_pending'] }}</div>
                            <div class="stat-label">{{ __('admin.pending_review') }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.ami.auditor.index') }}" class="text-decoration-none">
                <div class="stat-card bg-slate">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value">{{ $amiStats['auditor_cert_expiring'] }}</div>
                            <div class="stat-label">{{ __('admin.auditor_cert_expiring') }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Perlu Perhatian -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-bell me-2"></i>{{ __('admin.needs_attention') }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h6 class="text-muted mb-3">{{ __('admin.accreditation_expiring') }}</h6>
                    <ul class="list-group list-group-flush">
                        @forelse($akreditasiExpiringList as $akr)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-semibold">{{ $akr->prodi->nama ?? '-' }}</div>
                                <small class="text-muted">{{ $akr->tanggal_kadaluarsa->format('d M Y') }}</small>
                            </div>
                            <a href="{{ route('admin.akreditasi.show', $akr) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                        </li>
                        @empty
                        <li class="list-group-item px-0 text-center text-muted py-3">{{ __('admin.no_data') }}</li>
                        @endforelse
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">{{ __('admin.finding_overdue') }}</h6>
                    <ul class="list-group list-group-flush">
                        @forelse($temuanOverdueList as $temuan)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-semibold">{{ Str::limit($temuan->standar, 30) }}</div>
                                <small class="text-muted">{{ $temuan->jadwalAmi->prodi->nama ?? '-' }}</small>
                            </div>
                            <a href="{{ route('admin.ami.temuan.show', $temuan) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                        </li>
                        @empty
                        <li class="list-group-item px-0 text-center text-muted py-3">{{ __('admin.no_data') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h6 class="text-muted mb-3">{{ __('admin.pending_review') }}</h6>
                    <ul class="list-group list-group-flush">
                        @forelse($tindakLanjutPendingList as $tl)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-semibold">{{ Str::limit($tl->deskripsi, 30) }}</div>
                                <small class="text-muted">{{ $tl->user->name ?? '-' }} &middot; {{ $tl->temuanAmi->jadwalAmi->prodi->nama ?? '-' }}</small>
                            </div>
                            <a href="{{ route('admin.ami.tindak-lanjut.show', $tl) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                        </li>
                        @empty
                        <li class="list-group-item px-0 text-center text-muted py-3">{{ __('admin.no_data') }}</li>
                        @endforelse
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">{{ __('admin.auditor_cert_expiring') }}</h6>
                    <ul class="list-group list-group-flush">
                        @forelse($auditorCertExpiringList as $auditor)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-semibold">{{ $auditor->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $auditor->masa_berlaku->format('d M Y') }}</small>
                            </div>
                            <a href="{{ route('admin.ami.auditor.show', $auditor) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                        </li>
                        @empty
                        <li class="list-group-item px-0 text-center text-muted py-3">{{ __('admin.no_data') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
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
    @endif
@endsection

@if(auth()->user()->isAdmin())
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
@endif
