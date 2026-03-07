@extends('layouts.admin')

@section('title', __('admin.edit_announcement'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.edit_announcement') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.pengumuman.index') }}">{{ __('admin.announcements') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.edit') }}</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('admin.pengumuman.update', $pengumuman) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">{{ __('admin.announcement_content') }}</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="judul" class="form-label">{{ __('admin.title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $pengumuman->judul) }}" required>
                            @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="konten" class="form-label">{{ __('admin.content') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('konten') is-invalid @enderror" id="konten" name="konten" rows="15">{{ old('konten', $pengumuman->konten) }}</textarea>
                            @error('konten')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">{{ __('admin.publish') }}</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $pengumuman->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('admin.publish_now') }}</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_important" name="is_important" value="1" {{ old('is_important', $pengumuman->is_important) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_important">
                                    <i class="bi bi-exclamation-triangle text-warning me-1"></i>{{ __('admin.mark_important') }}
                                </label>
                            </div>
                        </div>

                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>{{ __('admin.created_at') }}: {{ $pengumuman->created_at->format('d M Y H:i') }}
                        </small>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">{{ __('admin.attachment') }}</div>
                    <div class="card-body">
                        @if($pengumuman->lampiran)
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.current_attachment') }}</label>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark fs-4"></i>
                                <a href="{{ Storage::url($pengumuman->lampiran) }}" target="_blank">{{ basename($pengumuman->lampiran) }}</a>
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="lampiran" class="form-label">{{ $pengumuman->lampiran ? __('admin.new_attachment') : __('admin.attachment') }}</label>
                            <input type="file" class="form-control @error('lampiran') is-invalid @enderror" id="lampiran" name="lampiran">
                            @error('lampiran')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('admin.leave_empty') }}</small>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>{{ __('admin.update') }}
                    </button>
                    <a href="{{ route('admin.pengumuman.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('admin.back') }}
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#konten').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['codeview', 'help']]
            ]
        });
    });
</script>
@endpush
