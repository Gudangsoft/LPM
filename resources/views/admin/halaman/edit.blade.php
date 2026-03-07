@extends('layouts.admin')

@section('title', __('admin.edit_page'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.edit_page') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.halaman.index') }}">{{ __('admin.pages') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.edit') }}</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('admin.halaman.update', $halaman) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-id">Indonesia</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-en">English</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="tab-id">
                                <div class="mb-3">
                                    <label for="judul" class="form-label">{{ __('admin.title') }} (ID) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $halaman->judul) }}" required>
                                    @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="konten" class="form-label">{{ __('admin.content') }} (ID) <span class="text-danger">*</span></label>
                                    <textarea class="form-control editor @error('konten') is-invalid @enderror" id="konten" name="konten" rows="15">{{ old('konten', $halaman->konten) }}</textarea>
                                    @error('konten')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-en">
                                <div class="mb-3">
                                    <label for="judul_en" class="form-label">{{ __('admin.title') }} (EN)</label>
                                    <input type="text" class="form-control @error('judul_en') is-invalid @enderror" id="judul_en" name="judul_en" value="{{ old('judul_en', $halaman->judul_en) }}">
                                    @error('judul_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="konten_en" class="form-label">{{ __('admin.content') }} (EN)</label>
                                    <textarea class="form-control editor @error('konten_en') is-invalid @enderror" id="konten_en" name="konten_en" rows="15">{{ old('konten_en', $halaman->konten_en) }}</textarea>
                                    @error('konten_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
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
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $halaman->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('admin.publish_now') }}</label>
                            </div>
                        </div>

                        <small class="text-muted">
                            <i class="bi bi-link-45deg me-1"></i>Slug: {{ $halaman->slug }}<br>
                            <i class="bi bi-calendar me-1"></i>{{ __('admin.updated_at') }}: {{ $halaman->updated_at->format('d M Y H:i') }}
                        </small>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>{{ __('admin.update') }}
                    </button>
                    <a href="{{ route('admin.halaman.index') }}" class="btn btn-outline-secondary">
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
        $('.editor').summernote({
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
