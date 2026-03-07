@extends('layouts.admin')

@section('title', 'Edit Jadwal AMI')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Jadwal AMI</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.jadwal.index') }}">Jadwal AMI</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <form action="{{ route('admin.ami.jadwal.update', $jadwal) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="periode_ami_id" class="form-label">Periode AMI <span class="text-danger">*</span></label>
                        <select class="form-select @error('periode_ami_id') is-invalid @enderror" id="periode_ami_id" name="periode_ami_id" required>
                            <option value="">Pilih Periode</option>
                            @foreach($periodes as $p)
                            <option value="{{ $p->id }}" {{ old('periode_ami_id', $jadwal->periode_ami_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }} ({{ $p->tahun_akademik }})
                            </option>
                            @endforeach
                        </select>
                        @error('periode_ami_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="prodi_id" class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select class="form-select @error('prodi_id') is-invalid @enderror" id="prodi_id" name="prodi_id" required>
                            <option value="">Pilih Prodi</option>
                            @foreach($prodis as $p)
                            <option value="{{ $p->id }}" {{ old('prodi_id', $jadwal->prodi_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->jenjang }} - {{ $p->nama }}
                            </option>
                            @endforeach
                        </select>
                        @error('prodi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_audit" class="form-label">Tanggal Audit <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_audit') is-invalid @enderror" id="tanggal_audit" name="tanggal_audit" value="{{ old('tanggal_audit', $jadwal->tanggal_audit->format('Y-m-d')) }}" required>
                        @error('tanggal_audit')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="waktu_mulai" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('waktu_mulai') is-invalid @enderror" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai', $jadwal->waktu_mulai) }}" required>
                        @error('waktu_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="waktu_selesai" class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('waktu_selesai') is-invalid @enderror" id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai', $jadwal->waktu_selesai) }}" required>
                        @error('waktu_selesai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="tempat" class="form-label">Tempat</label>
                    <input type="text" class="form-control @error('tempat') is-invalid @enderror" id="tempat" name="tempat" value="{{ old('tempat', $jadwal->tempat) }}">
                    @error('tempat')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="terjadwal" {{ old('status', $jadwal->status) == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="berlangsung" {{ old('status', $jadwal->status) == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                        <option value="selesai" {{ old('status', $jadwal->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditunda" {{ old('status', $jadwal->status) == 'ditunda' ? 'selected' : '' }}>Ditunda</option>
                        <option value="batal" {{ old('status', $jadwal->status) == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                    @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3">{{ old('catatan', $jadwal->catatan) }}</textarea>
                    @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
                <a href="{{ route('admin.ami.jadwal.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
