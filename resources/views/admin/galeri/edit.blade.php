@extends('layouts.admin')

@section('title', __('admin.edit_gallery'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.edit_gallery') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.galeri.index') }}">{{ __('admin.gallery') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.edit') }}</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">{{ __('admin.gallery_form') }}</div>
                <div class="card-body">
                    <form action="{{ route('admin.galeri.update', $galeri) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="judul" class="form-label">{{ __('admin.title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $galeri->judul) }}" required>
                            @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">{{ __('admin.description') }}</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $galeri->deskripsi) }}</textarea>
                            @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kategori" class="form-label">{{ __('admin.category') }}</label>
                                    <input type="text" class="form-control @error('kategori') is-invalid @enderror" id="kategori" name="kategori" value="{{ old('kategori', $galeri->kategori) }}" list="kategoriList">
                                    @if(isset($kategoris) && $kategoris->count())
                                    <datalist id="kategoriList">
                                        @foreach($kategoris as $k)
                                        <option value="{{ $k }}">
                                        @endforeach
                                    </datalist>
                                    @endif
                                    @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="urutan" class="form-label">{{ __('admin.order') }}</label>
                                    <input type="number" class="form-control @error('urutan') is-invalid @enderror" id="urutan" name="urutan" value="{{ old('urutan', $galeri->urutan) }}" min="0">
                                    @error('urutan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $galeri->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
                                <div class="form-text">{{ __('admin.gallery_active_help') }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.current_image') }}</label>
                            <div>
                                <img src="{{ Storage::url($galeri->gambar) }}" alt="" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="gambar" class="form-label">{{ __('admin.new_image') }}</label>
                            <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept="image/*" onchange="previewImage(this)">
                            @error('gambar')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('admin.leave_empty') }}</small>
                        </div>

                        <div class="mb-3">
                            <img id="preview" src="" alt="" class="img-fluid rounded d-none" style="max-height: 300px;">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('admin.update') }}
                            </button>
                            <a href="{{ route('admin.galeri.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>{{ __('admin.back') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
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
</script>
@endpush
