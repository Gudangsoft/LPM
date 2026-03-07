@extends('layouts.admin')

@section('title', 'Detail Auditor')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ $auditor->user->name ?? 'Auditor' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ami.auditor.index') }}">Auditor</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.auditor.edit', $auditor) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5>Total Penugasan</h5>
                    <h2>{{ $stats['total_penugasan'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5>Selesai</h5>
                    <h2>{{ $stats['penugasan_selesai'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5>Total Temuan</h5>
                    <h2>{{ $stats['total_temuan'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Auditor</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Nama</td>
                            <td>{{ $auditor->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>{{ $auditor->user->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">NIP</td>
                            <td>{{ $auditor->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bidang Keahlian</td>
                            <td>{{ $auditor->bidang_keahlian ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. Sertifikat</td>
                            <td>{{ $auditor->no_sertifikat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Masa Berlaku</td>
                            <td>
                                @if($auditor->masa_berlaku)
                                {{ $auditor->masa_berlaku->format('d M Y') }}
                                @if(!$auditor->isCertificateValid())
                                <span class="badge bg-danger">Expired</span>
                                @endif
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span class="badge bg-{{ $auditor->status_color }}">{{ ucfirst($auditor->status) }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Riwayat Penugasan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Prodi</th>
                                    <th>Tanggal</th>
                                    <th>Peran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($auditor->penugasan as $p)
                                <tr>
                                    <td>{{ $p->jadwalAmi->prodi->nama ?? '-' }}</td>
                                    <td>{{ $p->jadwalAmi->tanggal_audit?->format('d M Y') }}</td>
                                    <td><span class="badge bg-{{ $p->peran_color }}">{{ ucfirst($p->peran) }}</span></td>
                                    <td><span class="badge bg-{{ $p->status_color }}">{{ ucfirst($p->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.ami.jadwal.show', $p->jadwalAmi) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada penugasan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
