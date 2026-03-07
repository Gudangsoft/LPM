@extends('layouts.admin')

@section('title', 'Dashboard Akreditasi')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Dashboard Akreditasi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.akreditasi.index') }}">Akreditasi</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Total Prodi</h6>
                            <h2 class="mb-0">{{ $stats['total_prodi'] }}</h2>
                        </div>
                        <i class="bi bi-building fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Akreditasi Aktif</h6>
                            <h2 class="mb-0">{{ $stats['akreditasi_aktif'] }}</h2>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="opacity-75">Akan Kadaluarsa</h6>
                            <h2 class="mb-0">{{ $stats['akan_kadaluarsa'] }}</h2>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Sudah Kadaluarsa</h6>
                            <h2 class="mb-0">{{ $stats['sudah_kadaluarsa'] }}</h2>
                        </div>
                        <i class="bi bi-x-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Akreditasi Akan Kadaluarsa -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Akreditasi Akan Kadaluarsa (6 Bulan)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Program Studi</th>
                                    <th>Peringkat</th>
                                    <th>Kadaluarsa</th>
                                    <th>Sisa Hari</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasiExpiring as $item)
                                <tr>
                                    <td>
                                        <span class="badge bg-info me-1">{{ $item->prodi->jenjang }}</span>
                                        {{ $item->prodi->nama }}
                                    </td>
                                    <td><span class="badge bg-primary">{{ $item->peringkat }}</span></td>
                                    <td>{{ $item->tanggal_kadaluarsa->format('d M Y') }}</td>
                                    <td>
                                        @php $days = $item->days_until_expiration; @endphp
                                        <span class="badge bg-{{ $days <= 30 ? 'danger' : ($days <= 90 ? 'warning' : 'info') }}">
                                            {{ $days }} hari
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                                        Tidak ada akreditasi yang akan kadaluarsa dalam 6 bulan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Berdasarkan Peringkat</h5>
                </div>
                <div class="card-body">
                    @forelse($akreditasiByPeringkat as $item)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary fs-6">{{ $item->peringkat }}</span>
                        <span class="fw-bold">{{ $item->total }} Prodi</span>
                    </div>
                    @empty
                    <p class="text-muted text-center">Belum ada data</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Berdasarkan Lembaga</h5>
                </div>
                <div class="card-body">
                    @forelse($akreditasiByLembaga as $item)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ $item->lembaga }}</span>
                        <span class="badge bg-secondary">{{ $item->total }}</span>
                    </div>
                    @empty
                    <p class="text-muted text-center">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
