@extends('layouts.admin')

@section('title', 'Detail Akreditasi')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Detail Akreditasi</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.akreditasi.index') }}">Akreditasi</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.akreditasi.edit', $akreditasi) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Akreditasi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td style="width: 200px;" class="text-muted">Program Studi</td>
                            <td>
                                <span class="badge bg-info me-1">{{ $akreditasi->prodi->jenjang }}</span>
                                <strong>{{ $akreditasi->prodi->nama }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Lembaga Akreditasi</td>
                            <td>{{ $akreditasi->lembaga }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Peringkat</td>
                            <td><span class="badge bg-primary fs-5">{{ $akreditasi->peringkat }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor SK</td>
                            <td>{{ $akreditasi->nomor_sk }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal SK</td>
                            <td>{{ $akreditasi->tanggal_sk->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Kadaluarsa</td>
                            <td>
                                {{ $akreditasi->tanggal_kadaluarsa->format('d F Y') }}
                                @if($akreditasi->isExpired())
                                <span class="badge bg-danger ms-2">Sudah Kadaluarsa</span>
                                @elseif($akreditasi->isExpiringSoon())
                                <span class="badge bg-warning ms-2">{{ $akreditasi->days_until_expiration }} hari lagi</span>
                                @else
                                <span class="badge bg-success ms-2">{{ $akreditasi->days_until_expiration }} hari lagi</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span class="badge bg-{{ $akreditasi->status_color }}">{{ ucfirst(str_replace('_', ' ', $akreditasi->status)) }}</span></td>
                        </tr>
                        @if($akreditasi->file_sk)
                        <tr>
                            <td class="text-muted">File SK</td>
                            <td>
                                <a href="{{ Storage::url($akreditasi->file_sk) }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-file-pdf me-1"></i>Download SK
                                </a>
                            </td>
                        </tr>
                        @endif
                        @if($akreditasi->catatan)
                        <tr>
                            <td class="text-muted">Catatan</td>
                            <td>{{ $akreditasi->catatan }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Countdown</h5>
                </div>
                <div class="card-body text-center">
                    @if($akreditasi->isExpired())
                    <div class="text-danger">
                        <i class="bi bi-exclamation-circle fs-1"></i>
                        <h4 class="mt-2">Sudah Kadaluarsa</h4>
                        <p>Sejak {{ abs($akreditasi->days_until_expiration) }} hari yang lalu</p>
                    </div>
                    @else
                    <h1 class="display-3 {{ $akreditasi->days_until_expiration <= 90 ? 'text-warning' : 'text-primary' }}">
                        {{ $akreditasi->days_until_expiration }}
                    </h1>
                    <p class="text-muted mb-0">hari tersisa</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
