@extends('layouts.admin')

@section('title', 'Detail Temuan AMI')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Detail Temuan AMI</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ami.temuan.index') }}">Temuan AMI</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.ami.temuan.edit', $temuan) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Temuan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 35%;">Prodi</td>
                            <td>
                                <span class="badge bg-info">{{ $temuan->jadwalAmi->prodi->jenjang ?? '-' }}</span>
                                {{ $temuan->jadwalAmi->prodi->nama ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Audit</td>
                            <td>{{ $temuan->jadwalAmi->tanggal_audit?->format('d F Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Auditor</td>
                            <td>{{ $temuan->auditor->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Standar</td>
                            <td>{{ $temuan->standar }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kategori</td>
                            <td><span class="badge bg-{{ $temuan->kategori_color }}">{{ ucfirst($temuan->kategori) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                <span class="badge bg-{{ $temuan->status_color }}">{{ ucfirst(str_replace('_', ' ', $temuan->status)) }}</span>
                                @if($temuan->isOverdue())
                                <span class="badge bg-danger ms-1">Overdue</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Batas Tindak Lanjut</td>
                            <td>{{ $temuan->batas_tindak_lanjut?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex gap-2">
                        @if($temuan->status != 'verified')
                        <form action="{{ route('admin.ami.temuan.verify', $temuan) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="bi bi-check-lg"></i> Verify
                            </button>
                        </form>
                        @endif
                        @if($temuan->status != 'closed')
                        <form action="{{ route('admin.ami.temuan.close', $temuan) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-secondary">
                                <i class="bi bi-lock"></i> Close
                            </button>
                        </form>
                        @endif
                        @if($temuan->status == 'closed')
                        <form action="{{ route('admin.ami.temuan.reopen', $temuan) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">
                                <i class="bi bi-unlock"></i> Reopen
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Deskripsi Temuan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted">Deskripsi</h6>
                        <p>{{ $temuan->deskripsi }}</p>
                    </div>
                    @if($temuan->bukti)
                    <div class="mb-4">
                        <h6 class="text-muted">Catatan Bukti</h6>
                        <p>{{ $temuan->bukti }}</p>
                    </div>
                    @endif
                    @if($temuan->bukti_file)
                    <div class="mb-4">
                        <h6 class="text-muted">File Bukti/Evidence</h6>
                        <a href="{{ Storage::url($temuan->bukti_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-paperclip me-1"></i>Lihat File
                        </a>
                    </div>
                    @endif
                    @if($temuan->akar_masalah)
                    <div class="mb-4">
                        <h6 class="text-muted">Akar Masalah</h6>
                        <p>{{ $temuan->akar_masalah }}</p>
                    </div>
                    @endif
                    @if($temuan->rekomendasi)
                    <div>
                        <h6 class="text-muted">Rekomendasi</h6>
                        <p>{{ $temuan->rekomendasi }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tindak Lanjut -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tindak Lanjut ({{ $temuan->tindakLanjut->count() }})</h5>
                    <a href="{{ route('admin.ami.tindak-lanjut.create') }}?temuan_id={{ $temuan->id }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Tambah Tindak Lanjut
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($temuan->tindakLanjut->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($temuan->tindakLanjut as $tl)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between mb-2">
                                <h6 class="mb-0">{{ Str::limit($tl->deskripsi, 60) }}</h6>
                                <span class="badge bg-{{ $tl->status_color }}">{{ ucfirst($tl->status) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> {{ $tl->user->name ?? '-' }}
                                    @if($tl->tanggal_submit)
                                    | <i class="bi bi-calendar"></i> {{ $tl->tanggal_submit->format('d M Y') }}
                                    @endif
                                </small>
                                <a href="{{ route('admin.ami.tindak-lanjut.show', $tl) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="p-4 text-center text-muted">
                        Belum ada tindak lanjut untuk temuan ini
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
