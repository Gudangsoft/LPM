@extends('layouts.admin')

@section('title', 'Tambah Temuan AMI')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Tambah Temuan AMI</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.temuan.index') }}">Temuan AMI</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <form action="{{ route('admin.ami.temuan.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jadwal_ami_id" class="form-label">Jadwal Audit <span class="text-danger">*</span></label>
                        <select class="form-select @error('jadwal_ami_id') is-invalid @enderror" id="jadwal_ami_id" name="jadwal_ami_id" required>
                            <option value="">Pilih Jadwal</option>
                            @foreach($jadwals as $j)
                            <option value="{{ $j->id }}" {{ old('jadwal_ami_id', request('jadwal_id')) == $j->id ? 'selected' : '' }}>
                                {{ $j->prodi->nama }} - {{ $j->tanggal_audit->format('d M Y') }}
                            </option>
                            @endforeach
                        </select>
                        @error('jadwal_ami_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="auditor_id" class="form-label">Auditor Pelapor <span class="text-danger">*</span></label>
                        <select class="form-select @error('auditor_id') is-invalid @enderror" id="auditor_id" name="auditor_id" required>
                            <option value="">Pilih Auditor</option>
                            @foreach($auditors as $a)
                            <option value="{{ $a->id }}" {{ old('auditor_id') == $a->id ? 'selected' : '' }}>
                                {{ $a->user->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('auditor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="standar" class="form-label">Standar <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('standar') is-invalid @enderror" id="standar" name="standar" value="{{ old('standar') }}" placeholder="Contoh: Standar Pembelajaran" required>
                        @error('standar')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="klausul" class="form-label">Klausul/Butir</label>
                        <input type="text" class="form-control @error('klausul') is-invalid @enderror" id="klausul" name="klausul" value="{{ old('klausul') }}" placeholder="Contoh: 4.1.2">
                        @error('klausul')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kategori" class="form-label">Kategori Temuan <span class="text-danger">*</span></label>
                        <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="mayor" {{ old('kategori') == 'mayor' ? 'selected' : '' }}>Mayor</option>
                            <option value="minor" {{ old('kategori') == 'minor' ? 'selected' : '' }}>Minor</option>
                            <option value="observasi" {{ old('kategori') == 'observasi' ? 'selected' : '' }}>Observasi</option>
                            <option value="rekomendasi" {{ old('kategori') == 'rekomendasi' ? 'selected' : '' }}>Rekomendasi</option>
                        </select>
                        @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <strong>Mayor:</strong> Ketidaksesuaian total/sebagian besar<br>
                            <strong>Minor:</strong> Ketidaksesuaian sebagian<br>
                            <strong>Observasi:</strong> Potensi ketidaksesuaian<br>
                            <strong>Rekomendasi:</strong> Saran perbaikan
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="open" {{ old('status', 'open') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="verified" {{ old('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Temuan <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="bukti" class="form-label">Bukti/Evidence</label>
                    <textarea class="form-control @error('bukti') is-invalid @enderror" id="bukti" name="bukti" rows="3">{{ old('bukti') }}</textarea>
                    @error('bukti')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Tuliskan bukti-bukti yang mendukung temuan</div>
                </div>

                <div class="mb-3">
                    <label for="rekomendasi" class="form-label">Rekomendasi Perbaikan</label>
                    <textarea class="form-control @error('rekomendasi') is-invalid @enderror" id="rekomendasi" name="rekomendasi" rows="3">{{ old('rekomendasi') }}</textarea>
                    @error('rekomendasi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="target_selesai" class="form-label">Target Penyelesaian</label>
                        <input type="date" class="form-control @error('target_selesai') is-invalid @enderror" id="target_selesai" name="target_selesai" value="{{ old('target_selesai') }}">
                        @error('target_selesai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
                <a href="{{ route('admin.ami.temuan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
