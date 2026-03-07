@extends('layouts.admin')

@section('title', __('admin.add_document_type'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.add_document_type') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.jenis-dokumen.index') }}">{{ __('admin.document_types') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.add_new') }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">{{ __('admin.document_type_form') }}</div>
                <div class="card-body">
                    <form action="{{ route('admin.jenis-dokumen.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                            @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.description') }}</label>
                            <textarea name="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.icon') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="iconPreview"><i class="bi bi-file-earmark"></i></span>
                                        <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', 'bi-file-earmark') }}" placeholder="bi-file-earmark" id="iconInput">
                                    </div>
                                    <small class="text-muted">Bootstrap Icons (e.g., bi-file-pdf, bi-file-word)</small>
                                    @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.order') }}</label>
                                    <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror" value="{{ old('urutan', 0) }}" min="0">
                                    @error('urutan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">{{ __('admin.active') }}</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}
                            </button>
                            <a href="{{ route('admin.jenis-dokumen.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i>{{ __('admin.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">{{ __('admin.common_icons') }}</div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @php
                        $icons = [
                            'bi-file-earmark', 'bi-file-earmark-pdf', 'bi-file-earmark-word',
                            'bi-file-earmark-excel', 'bi-file-earmark-ppt', 'bi-file-earmark-image',
                            'bi-file-earmark-zip', 'bi-file-earmark-text', 'bi-file-earmark-code',
                            'bi-folder', 'bi-folder2', 'bi-journal', 'bi-book', 'bi-files',
                            'bi-archive', 'bi-clipboard', 'bi-newspaper', 'bi-card-text'
                        ];
                        @endphp
                        @foreach($icons as $icon)
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="{{ $icon }}">
                            <i class="bi {{ $icon }}"></i>
                        </button>
                        @endforeach
                    </div>
                    <small class="text-muted mt-2 d-block">Click icon to use</small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const iconInput = document.getElementById('iconInput');
        const iconPreview = document.getElementById('iconPreview');

        // Icon preview
        iconInput.addEventListener('input', function() {
            iconPreview.innerHTML = '<i class="bi ' + this.value + '"></i>';
        });

        // Icon buttons
        document.querySelectorAll('.icon-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const icon = this.getAttribute('data-icon');
                iconInput.value = icon;
                iconPreview.innerHTML = '<i class="bi ' + icon + '"></i>';
            });
        });
    });
</script>
@endpush
