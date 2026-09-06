@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Laporan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center d-flex flex-column">
                    <i class="bi bi-search fs-1 text-primary mb-3"></i>
                    <h5 class="card-title">Laporan Temuan AMI</h5>
                    <p class="card-text text-muted small flex-grow-1">Daftar seluruh temuan audit mutu internal beserta status dan penanggung jawabnya.</p>
                    <a href="{{ route('admin.laporan.temuan.export') }}" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i>Export CSV
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center d-flex flex-column">
                    <i class="bi bi-clipboard-check fs-1 text-info mb-3"></i>
                    <h5 class="card-title">Laporan Tindak Lanjut</h5>
                    <p class="card-text text-muted small flex-grow-1">Status pengajuan dan review tindak lanjut atas temuan audit.</p>
                    <a href="{{ route('admin.laporan.tindak-lanjut.export') }}" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i>Export CSV
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center d-flex flex-column">
                    <i class="bi bi-award fs-1 text-warning mb-3"></i>
                    <h5 class="card-title">Laporan Akreditasi</h5>
                    <p class="card-text text-muted small flex-grow-1">Status akreditasi seluruh program studi beserta masa berlakunya.</p>
                    <a href="{{ route('admin.laporan.akreditasi.export') }}" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i>Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
