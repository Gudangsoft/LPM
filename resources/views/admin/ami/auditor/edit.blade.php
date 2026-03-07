@extends('layouts.admin')

@section('title', 'Edit Auditor')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Auditor</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.auditor.index') }}">Auditor</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <form action="{{ route('admin.ami.auditor.update', $auditor) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Auditor:</strong> {{ $auditor->user->name }} ({{ $auditor->user->email }})
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip" name="nip" value="{{ old('nip', $auditor->nip) }}">
                        @error('nip')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bidang_keahlian" class="form-label">Bidang Keahlian</label>
                        <input type="text" class="form-control @error('bidang_keahlian') is-invalid @enderror" id="bidang_keahlian" name="bidang_keahlian" value="{{ old('bidang_keahlian', $auditor->bidang_keahlian) }}">
                        @error('bidang_keahlian')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="no_sertifikat" class="form-label">No. Sertifikat</label>
                        <input type="text" class="form-control @error('no_sertifikat') is-invalid @enderror" id="no_sertifikat" name="no_sertifikat" value="{{ old('no_sertifikat', $auditor->no_sertifikat) }}">
                        @error('no_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_sertifikat" class="form-label">Tanggal Sertifikat</label>
                        <input type="date" class="form-control @error('tanggal_sertifikat') is-invalid @enderror" id="tanggal_sertifikat" name="tanggal_sertifikat" value="{{ old('tanggal_sertifikat', $auditor->tanggal_sertifikat?->format('Y-m-d')) }}">
                        @error('tanggal_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="masa_berlaku" class="form-label">Masa Berlaku</label>
                        <input type="date" class="form-control @error('masa_berlaku') is-invalid @enderror" id="masa_berlaku" name="masa_berlaku" value="{{ old('masa_berlaku', $auditor->masa_berlaku?->format('Y-m-d')) }}">
                        @error('masa_berlaku')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="aktif" {{ old('status', $auditor->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $auditor->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3">{{ old('catatan', $auditor->catatan) }}</textarea>
                    @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
                <a href="{{ route('admin.ami.auditor.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
