@extends('layouts.admin')

@section('title', 'Edit Program Studi')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Program Studi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.prodi.index') }}">Program Studi</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <form action="{{ route('admin.prodi.update', $prodi) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kode" class="form-label">Kode Prodi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode" name="kode" value="{{ old('kode', $prodi->kode) }}" required>
                        @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jenjang" class="form-label">Jenjang <span class="text-danger">*</span></label>
                        <select class="form-select @error('jenjang') is-invalid @enderror" id="jenjang" name="jenjang" required>
                            <option value="">Pilih Jenjang</option>
                            @foreach($jenjangOptions as $j)
                            <option value="{{ $j }}" {{ old('jenjang', $prodi->jenjang) == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                        @error('jenjang')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Program Studi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $prodi->nama) }}" required>
                    @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fakultas" class="form-label">Fakultas</label>
                    <input type="text" class="form-control @error('fakultas') is-invalid @enderror" id="fakultas" name="fakultas" value="{{ old('fakultas', $prodi->fakultas) }}">
                    @error('fakultas')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="kaprodi_id" class="form-label">Kepala Program Studi</label>
                    <select class="form-select @error('kaprodi_id') is-invalid @enderror" id="kaprodi_id" name="kaprodi_id">
                        <option value="">Pilih Kaprodi</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('kaprodi_id', $prodi->kaprodi_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('kaprodi_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $prodi->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
                <a href="{{ route('admin.prodi.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
