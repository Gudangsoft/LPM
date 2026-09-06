@extends('layouts.admin')

@section('title', 'Edit Bab Panduan')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Bab Panduan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.panduan.index') }}">Buku Panduan</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('admin.panduan.update', $panduan) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">Isi Bab</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Bab <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $panduan->judul) }}" required>
                            @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="konten" class="form-label">Konten <span class="text-danger">*</span></label>
                            <textarea class="form-control editor @error('konten') is-invalid @enderror" id="konten" name="konten" rows="15">{{ old('konten', $panduan->konten) }}</textarea>
                            @error('konten')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">Pengaturan</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kategori') is-invalid @enderror" id="kategori" name="kategori" list="kategori-list" value="{{ old('kategori', $panduan->kategori) }}" required>
                            <datalist id="kategori-list">
                                @foreach($kategoris as $k)
                                <option value="{{ $k }}">
                                @endforeach
                            </datalist>
                            @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label">Ikon (Bootstrap Icons)</label>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $panduan->icon) }}" placeholder="mis. bi-info-circle">
                            @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="urutan" class="form-label">{{ __('admin.order') }}</label>
                            <input type="number" class="form-control @error('urutan') is-invalid @enderror" id="urutan" name="urutan" value="{{ old('urutan', $panduan->urutan) }}" min="0">
                            @error('urutan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $panduan->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}
                    </button>
                    <a href="{{ route('admin.panduan.index') }}" class="btn btn-outline-secondary">
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
                ['insert', ['link']],
                ['view', ['codeview', 'help']]
            ]
        });
    });
</script>
@endpush
