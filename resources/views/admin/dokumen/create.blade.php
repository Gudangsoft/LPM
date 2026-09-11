@extends('layouts.admin')

@section('title', __('admin.add_document'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.add_document') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dokumen.index') }}">{{ __('admin.documents') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.add_new') }}</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">{{ __('admin.document_form') }}</div>
                <div class="card-body">
                    <form action="{{ route('admin.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="judul" class="form-label">{{ __('admin.title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" required>
                            @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">{{ __('admin.description') }}</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="jenis_dokumen_id" class="form-label">{{ __('admin.document_types') }}</label>
                            <select class="form-select @error('jenis_dokumen_id') is-invalid @enderror" id="jenis_dokumen_id" name="jenis_dokumen_id">
                                <option value="">-- {{ __('admin.select_document_type') }} --</option>
                                @foreach($jenisDokumen as $jenis)
                                <option value="{{ $jenis->id }}" {{ old('jenis_dokumen_id', request('jenis')) == $jenis->id ? 'selected' : '' }}>
                                    {{ $jenis->nama }}
                                </option>
                                @endforeach
                            </select>
                            @error('jenis_dokumen_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="standar_mutu_id" class="form-label">Standar Mutu Terkait</label>
                            <select class="form-select @error('standar_mutu_id') is-invalid @enderror" id="standar_mutu_id" name="standar_mutu_id">
                                <option value="">-- Tidak Terkait --</option>
                                @foreach($standarMutus as $standar)
                                <option value="{{ $standar->id }}" {{ old('standar_mutu_id') == $standar->id ? 'selected' : '' }}>
                                    {{ $standar->nama }}
                                </option>
                                @endforeach
                            </select>
                            @error('standar_mutu_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Opsional - untuk mengorganisir dokumen sebagai bukti standar akreditasi</small>
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">{{ __('admin.file') }} <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                            @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('admin.allowed_types') }}: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX (Max: 10MB)</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}
                            </button>
                            <a href="{{ route('admin.dokumen.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>{{ __('admin.back') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
