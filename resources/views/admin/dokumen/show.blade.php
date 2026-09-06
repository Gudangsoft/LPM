@extends('layouts.admin')

@section('title', $dokumen->judul)

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ $dokumen->judul }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dokumen.index') }}">{{ __('admin.documents') }}</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.dokumen.edit', $dokumen) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>{{ __('admin.edit') }}
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Informasi</div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span class="badge bg-{{ $dokumen->status_color }}">{{ ucfirst($dokumen->status) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('admin.document_types') }}</td>
                            <td>{{ $dokumen->jenisDokumen->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Standar Mutu</td>
                            <td>{{ $dokumen->standarMutu->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Diupload Oleh</td>
                            <td>{{ $dokumen->uploader->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Versi Saat Ini</td>
                            <td>{{ $dokumen->current_version }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Direview Oleh</td>
                            <td>{{ $dokumen->reviewer->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Review</td>
                            <td>{{ $dokumen->reviewed_at?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('admin.status') }} Publik</td>
                            <td>
                                @if($dokumen->is_active)
                                <span class="badge bg-success">{{ __('admin.active') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('admin.inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex flex-column gap-2">
                        @if($dokumen->status === 'draft')
                        <form action="{{ route('admin.dokumen.submit', $dokumen) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info w-100 text-white">
                                <i class="bi bi-send me-1"></i>Ajukan untuk Review
                            </button>
                        </form>
                        @endif

                        @if($dokumen->status === 'submitted')
                        <form action="{{ route('admin.dokumen.approve', $dokumen) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-lg me-1"></i>Setujui
                            </button>
                        </form>
                        <form action="{{ route('admin.dokumen.reject', $dokumen) }}" method="POST" onsubmit="return promptRejectReason(this)">
                            @csrf
                            <input type="hidden" name="catatan_reviewer" class="reject-reason-input">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-x-lg me-1"></i>Tolak
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            @if($dokumen->catatan_reviewer)
            <div class="alert alert-{{ $dokumen->status === 'rejected' ? 'danger' : 'info' }}">
                <strong><i class="bi bi-info-circle me-1"></i>Catatan Reviewer:</strong>
                <p class="mb-0 mt-1">{{ $dokumen->catatan_reviewer }}</p>
            </div>
            @endif
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>File Saat Ini</span>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $dokumen->file_name }}</strong></p>
                    <p class="text-muted small mb-3">{{ $dokumen->formatted_size }}</p>
                    <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download me-1"></i>Unduh
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Riwayat Versi ({{ $dokumen->versions->count() }})
                </div>
                <div class="card-body p-0">
                    @if($dokumen->versions->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($dokumen->versions as $version)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-secondary me-2">v{{ $version->version_number }}</span>
                                {{ $version->file_name }}
                                <div class="small text-muted">
                                    {{ $version->uploader->name ?? '-' }} &middot; {{ $version->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            <a href="{{ Storage::url($version->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="p-4 text-center text-muted">Belum ada riwayat versi sebelumnya</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function promptRejectReason(form) {
        const reason = prompt('Alasan penolakan:');
        if (!reason) {
            return false;
        }
        form.querySelector('.reject-reason-input').value = reason;
        return true;
    }
</script>
@endpush
