@extends('layouts.admin')

@section('title', __('admin.edit_slider'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.edit_slider') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">{{ __('admin.sliders') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.edit') }}</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">{{ __('admin.slider_form') }}</div>
                <div class="card-body">
                    <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.current_image') }}</label>
                            <div>
                                <img src="{{ Storage::url($slider->gambar) }}" alt="" class="img-fluid rounded" style="max-height: 200px;">
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
                            <img id="preview" src="" alt="" class="img-fluid rounded d-none" style="max-height: 200px;">
                        </div>

                        <div class="mb-3">
                            <label for="judul" class="form-label">{{ __('admin.title') }}</label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $slider->judul) }}">
                            @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">{{ __('admin.description') }}</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $slider->deskripsi) }}</textarea>
                            @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="link" class="form-label">{{ __('admin.link') }}</label>
                                    <input type="url" class="form-control @error('link') is-invalid @enderror" id="link" name="link" value="{{ old('link', $slider->link) }}" placeholder="https://">
                                    @error('link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="urutan" class="form-label">{{ __('admin.order') }}</label>
                                    <input type="number" class="form-control @error('urutan') is-invalid @enderror" id="urutan" name="urutan" value="{{ old('urutan', $slider->urutan) }}" min="0">
                                    @error('urutan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $slider->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('admin.update') }}
                            </button>
                            <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary">
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
