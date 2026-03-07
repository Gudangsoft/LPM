@extends('layouts.admin')

@section('title', 'Detail Tindak Lanjut')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Detail Tindak Lanjut</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ami.tindak-lanjut.index') }}">Tindak Lanjut</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.ami.tindak-lanjut.edit', $tindakLanjut) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span class="badge bg-{{ $tindakLanjut->status_color }}">{{ ucfirst($tindakLanjut->status) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Penanggung Jawab</td>
                            <td>{{ $tindakLanjut->penanggungJawab->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Mulai</td>
                            <td>{{ $tindakLanjut->tanggal_mulai?->format('d M Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Selesai</td>
                            <td>{{ $tindakLanjut->tanggal_selesai?->format('d M Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Reviewer</td>
                            <td>{{ $tindakLanjut->reviewer->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Review</td>
                            <td>{{ $tindakLanjut->tanggal_review?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                @if($tindakLanjut->status == 'diajukan')
                <div class="card-footer">
                    <form action="{{ route('admin.ami.tindak-lanjut.review', $tindakLanjut) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <textarea name="catatan_reviewer" class="form-control form-control-sm" rows="2" placeholder="Catatan reviewer (opsional)"></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="status" value="disetujui" class="btn btn-sm btn-success flex-fill">
                                <i class="bi bi-check-lg"></i> Setujui
                            </button>
                            <button type="submit" name="status" value="ditolak" class="btn btn-sm btn-danger flex-fill">
                                <i class="bi bi-x-lg"></i> Tolak
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>

            <!-- Temuan Terkait -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Temuan Terkait</h5>
                </div>
                <div class="card-body">
                    @if($tindakLanjut->temuanAmi)
                    <p class="mb-2">
                        <span class="badge bg-{{ $tindakLanjut->temuanAmi->kategori_color }}">{{ ucfirst($tindakLanjut->temuanAmi->kategori) }}</span>
                    </p>
                    <p class="mb-1"><strong>{{ $tindakLanjut->temuanAmi->standar }}</strong></p>
                    <p class="text-muted small mb-2">{{ Str::limit($tindakLanjut->temuanAmi->deskripsi, 100) }}</p>
                    <a href="{{ route('admin.ami.temuan.show', $tindakLanjut->temuanAmi) }}" class="btn btn-sm btn-outline-primary">
                        Lihat Detail Temuan
                    </a>
                    @else
                    <p class="text-muted">-</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Tindak Lanjut</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted">Tindakan</h6>
                        <p class="fs-5">{{ $tindakLanjut->tindakan }}</p>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-muted">Deskripsi</h6>
                        <p>{{ $tindakLanjut->deskripsi }}</p>
                    </div>
                    @if($tindakLanjut->bukti_tindak_lanjut)
                    <div class="mb-4">
                        <h6 class="text-muted">Bukti Tindak Lanjut</h6>
                        <p>{{ $tindakLanjut->bukti_tindak_lanjut }}</p>
                    </div>
                    @endif
                    @if($tindakLanjut->catatan)
                    <div class="mb-4">
                        <h6 class="text-muted">Catatan</h6>
                        <p>{{ $tindakLanjut->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($tindakLanjut->catatan_reviewer)
            <div class="card">
                <div class="card-header bg-{{ $tindakLanjut->status == 'disetujui' ? 'success' : ($tindakLanjut->status == 'ditolak' ? 'danger' : 'info') }} text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-{{ $tindakLanjut->status == 'disetujui' ? 'check-circle' : ($tindakLanjut->status == 'ditolak' ? 'x-circle' : 'info-circle') }}"></i>
                        Catatan Reviewer
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-1">{{ $tindakLanjut->catatan_reviewer }}</p>
                    <small class="text-muted">
                        oleh {{ $tindakLanjut->reviewer->name ?? '-' }} pada {{ $tindakLanjut->tanggal_review?->format('d M Y H:i') }}
                    </small>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
