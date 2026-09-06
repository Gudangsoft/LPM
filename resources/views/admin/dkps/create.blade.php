@extends('layouts.admin')

@section('title', 'Draft DKPS Baru')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Draft DKPS Baru</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dkps.index') }}">DKPS</a></li>
                <li class="breadcrumb-item active">Draft Baru</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Informasi Draft</div>
                <div class="card-body">
                    <form action="{{ route('admin.dkps.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="prodi_id" class="form-label">Program Studi <span class="text-danger">*</span></label>
                            <select class="form-select @error('prodi_id') is-invalid @enderror" id="prodi_id" name="prodi_id" required>
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->full_name }}</option>
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
                                    <input type="number" class="form-control @error('tahun_ts_awal') is-invalid @enderror" id="tahun_ts_awal" name="tahun_ts_awal" value="{{ old('tahun_ts_awal', date('Y')) }}" required>
                                    @error('tahun_ts_awal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tahun_ts_akhir" class="form-label">Tahun Akademik TS (Akhir) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('tahun_ts_akhir') is-invalid @enderror" id="tahun_ts_akhir" name="tahun_ts_akhir" value="{{ old('tahun_ts_akhir', date('Y') + 1) }}" required>
                                    @error('tahun_ts_akhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nama_pengusul" class="form-label">Nama Pengusul</label>
                            <input type="text" class="form-control @error('nama_pengusul') is-invalid @enderror" id="nama_pengusul" name="nama_pengusul" value="{{ old('nama_pengusul') }}">
                            @error('nama_pengusul')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_pengusulan" class="form-label">Tanggal Pengusulan</label>
                            <input type="date" class="form-control @error('tanggal_pengusulan') is-invalid @enderror" id="tanggal_pengusulan" name="tanggal_pengusulan" value="{{ old('tanggal_pengusulan') }}">
                            @error('tanggal_pengusulan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Buat & Mulai Isi
                            </button>
                            <a href="{{ route('admin.dkps.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>{{ __('admin.back') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
