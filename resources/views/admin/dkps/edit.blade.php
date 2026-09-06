@extends('layouts.admin')

@section('title', 'Edit Info DKPS')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Info DKPS</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dkps.index') }}">DKPS</a></li>
                <li class="breadcrumb-item active">Edit Info</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Informasi Draft</div>
                <div class="card-body">
                    <form action="{{ route('admin.dkps.update', $submission) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="prodi_id" class="form-label">Program Studi <span class="text-danger">*</span></label>
                            <select class="form-select @error('prodi_id') is-invalid @enderror" id="prodi_id" name="prodi_id" required>
                                @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ old('prodi_id', $submission->prodi_id) == $prodi->id ? 'selected' : '' }}>{{ $prodi->full_name }}</option>
                                @endforeach
                            </select>
                            @error('prodi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tahun_ts_awal" class="form-label">Tahun Akademik TS (Awal) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('tahun_ts_awal') is-invalid @enderror" id="tahun_ts_awal" name="tahun_ts_awal" value="{{ old('tahun_ts_awal', $submission->tahun_ts_awal) }}" required>
                                    @error('tahun_ts_awal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tahun_ts_akhir" class="form-label">Tahun Akademik TS (Akhir) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('tahun_ts_akhir') is-invalid @enderror" id="tahun_ts_akhir" name="tahun_ts_akhir" value="{{ old('tahun_ts_akhir', $submission->tahun_ts_akhir) }}" required>
                                    @error('tahun_ts_akhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nama_pengusul" class="form-label">Nama Pengusul</label>
                            <input type="text" class="form-control @error('nama_pengusul') is-invalid @enderror" id="nama_pengusul" name="nama_pengusul" value="{{ old('nama_pengusul', $submission->nama_pengusul) }}">
                            @error('nama_pengusul')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_pengusulan" class="form-label">Tanggal Pengusulan</label>
                            <input type="date" class="form-control @error('tanggal_pengusulan') is-invalid @enderror" id="tanggal_pengusulan" name="tanggal_pengusulan" value="{{ old('tanggal_pengusulan', optional($submission->tanggal_pengusulan)->format('Y-m-d')) }}">
                            @error('tanggal_pengusulan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('admin.status') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="draft" {{ old('status', $submission->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="final" {{ old('status', $submission->status) == 'final' ? 'selected' : '' }}>Final</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}
                            </button>
                            <a href="{{ route('admin.dkps.show', $submission) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>{{ __('admin.back') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
