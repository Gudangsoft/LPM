@extends('layouts.admin')

@section('title', __('admin.edit_news'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.edit_news') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.berita.index') }}">{{ __('admin.news') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.edit') }}</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('admin.berita.update', $berita) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">{{ __('admin.news_content') }}</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="judul" class="form-label">{{ __('admin.title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $berita->judul) }}" required>
                            @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="ringkasan" class="form-label">{{ __('admin.summary') }}</label>
                            <textarea class="form-control @error('ringkasan') is-invalid @enderror" id="ringkasan" name="ringkasan" rows="3" maxlength="500">{{ old('ringkasan', $berita->ringkasan) }}</textarea>
                            @error('ringkasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted"><span id="ringkasanCount">0</span>/500 karakter</small>
                        </div>

                        <div class="mb-3">
                            <label for="konten" class="form-label">{{ __('admin.content') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('konten') is-invalid @enderror" id="konten" name="konten" rows="15">{{ old('konten', $berita->konten) }}</textarea>
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
                            <label for="kategori_id" class="form-label">{{ __('admin.category') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('kategori_id') is-invalid @enderror" id="kategori_id" name="kategori_id" required>
                                <option value="">{{ __('admin.select_category') }}</option>
                                @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ old('kategori_id', $berita->kategori_id) == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $berita->is_published) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_published">{{ __('admin.publish_now') }}</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-eye me-1"></i>{{ $berita->views }} {{ __('admin.views') }}<br>
                                <i class="bi bi-calendar me-1"></i>{{ __('admin.created_at') }}: {{ $berita->created_at->format('d M Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">{{ __('admin.thumbnail') }}</div>
                    <div class="card-body">
                        @if($berita->thumbnail)
                        <div class="mb-3">
                            <img src="{{ Storage::url($berita->thumbnail) }}" alt="" class="img-fluid rounded mb-2">
                        </div>
                        @endif
                        <div class="mb-3">
                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail" accept="image/*" onchange="previewImage(this)">
                            @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('admin.max_file_size') }}: 2MB</small>
                        </div>
                        <img id="preview" src="" alt="" class="img-fluid rounded d-none">
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>{{ __('admin.update') }}
                    </button>
                    <a href="{{ route('admin.berita.index') }}" class="btn btn-outline-secondary">
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

    function previewImage(input) {
        const preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    const ringkasanField = document.getElementById('ringkasan');
    const ringkasanCount = document.getElementById('ringkasanCount');
    if (ringkasanField && ringkasanCount) {
        const updateCount = () => ringkasanCount.textContent = ringkasanField.value.length;
        ringkasanField.addEventListener('input', updateCount);
        updateCount();
    }
</script>
@endpush
