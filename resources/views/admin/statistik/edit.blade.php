@extends('layouts.admin')

@section('title', 'Edit Statistik')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Data Statistik</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.statistik.index') }}">Statistik</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <form action="{{ route('admin.statistik.update', $statistik) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoriOptions as $key => $label)
                            <option value="{{ $key }}" {{ old('kategori', $statistik->kategori) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('tahun') is-invalid @enderror" id="tahun" name="tahun" value="{{ old('tahun', $statistik->tahun) }}" min="2000" max="{{ now()->year + 5 }}" required>
                        @error('tahun')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="usulan" class="form-label">Jumlah Usulan <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('usulan') is-invalid @enderror" id="usulan" name="usulan" value="{{ old('usulan', $statistik->usulan) }}" min="0" required>
                        @error('usulan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="didanai" class="form-label">Jumlah Didanai <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('didanai') is-invalid @enderror" id="didanai" name="didanai" value="{{ old('didanai', $statistik->didanai) }}" min="0" required>
                        @error('didanai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="dana_usulan" class="form-label">Dana Usulan (Rp)</label>
                        <input type="number" class="form-control @error('dana_usulan') is-invalid @enderror" id="dana_usulan" name="dana_usulan" value="{{ old('dana_usulan', $statistik->dana_usulan) }}" min="0" step="0.01">
                        @error('dana_usulan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="dana_disetujui" class="form-label">Dana Disetujui (Rp)</label>
                        <input type="number" class="form-control @error('dana_disetujui') is-invalid @enderror" id="dana_disetujui" name="dana_disetujui" value="{{ old('dana_disetujui', $statistik->dana_disetujui) }}" min="0" step="0.01">
                        @error('dana_disetujui')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $statistik->keterangan) }}</textarea>
                    @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
                <a href="{{ route('admin.statistik.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
