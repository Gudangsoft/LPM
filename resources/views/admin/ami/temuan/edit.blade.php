@extends('layouts.admin')

@section('title', 'Edit Temuan AMI')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Temuan AMI</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.temuan.index') }}">Temuan AMI</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <form action="{{ route('admin.ami.temuan.update', $temuan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jadwal_ami_id" class="form-label">Jadwal Audit <span class="text-danger">*</span></label>
                        <select class="form-select @error('jadwal_ami_id') is-invalid @enderror" id="jadwal_ami_id" name="jadwal_ami_id" required>
                            <option value="">Pilih Jadwal</option>
                            @foreach($jadwals as $j)
                            <option value="{{ $j->id }}" {{ old('jadwal_ami_id', $temuan->jadwal_ami_id) == $j->id ? 'selected' : '' }}>
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
                            <option value="{{ $a->id }}" {{ old('auditor_id', $temuan->auditor_id) == $a->id ? 'selected' : '' }}>
                                {{ $a->user->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('auditor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="standar_mutu_id" class="form-label">Standar <span class="text-danger">*</span></label>
                    <select class="form-select @error('standar_mutu_id') is-invalid @enderror" id="standar_mutu_id" name="standar_mutu_id" required>
                        <option value="">Pilih Standar</option>
                        @foreach($standarMutus as $standar)
                        <option value="{{ $standar->id }}" {{ old('standar_mutu_id', $temuan->standar_mutu_id) == $standar->id ? 'selected' : '' }}>{{ $standar->nama }}</option>
                        @endforeach
                    </select>
                    @error('standar_mutu_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kategori" class="form-label">Kategori Temuan <span class="text-danger">*</span></label>
                        <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                            <option value="mayor" {{ old('kategori', $temuan->kategori) == 'mayor' ? 'selected' : '' }}>Mayor</option>
                            <option value="minor" {{ old('kategori', $temuan->kategori) == 'minor' ? 'selected' : '' }}>Minor</option>
                            <option value="observasi" {{ old('kategori', $temuan->kategori) == 'observasi' ? 'selected' : '' }}>Observasi</option>
                            <option value="rekomendasi" {{ old('kategori', $temuan->kategori) == 'rekomendasi' ? 'selected' : '' }}>Rekomendasi</option>
                        </select>
                        @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="open" {{ old('status', $temuan->status) == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ old('status', $temuan->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="verified" {{ old('status', $temuan->status) == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="closed" {{ old('status', $temuan->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Temuan <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi', $temuan->deskripsi) }}</textarea>
                    @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="bukti" class="form-label">Catatan Bukti</label>
                    <textarea class="form-control @error('bukti') is-invalid @enderror" id="bukti" name="bukti" rows="3">{{ old('bukti', $temuan->bukti) }}</textarea>
                    @error('bukti')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Catatan tambahan tentang bukti (opsional)</div>
                </div>

                @if($temuan->bukti_file)
                <div class="mb-3">
                    <label class="form-label">File Bukti Saat Ini</label>
                    <div>
                        <a href="{{ Storage::url($temuan->bukti_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-paperclip me-1"></i>Lihat File
                        </a>
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label for="bukti_file" class="form-label">{{ $temuan->bukti_file ? 'Ganti File Bukti/Evidence' : 'File Bukti/Evidence' }}</label>
                    <input type="file" class="form-control @error('bukti_file') is-invalid @enderror" id="bukti_file" name="bukti_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    @error('bukti_file')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Kosongkan jika tidak ingin mengubah. Format: PDF, DOC, DOCX, JPG, PNG. Maks 10MB.</div>
                </div>

                <div class="mb-3">
                    <label for="akar_masalah" class="form-label">Akar Masalah</label>
                    <textarea class="form-control @error('akar_masalah') is-invalid @enderror" id="akar_masalah" name="akar_masalah" rows="3">{{ old('akar_masalah', $temuan->akar_masalah) }}</textarea>
                    @error('akar_masalah')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="rekomendasi" class="form-label">Rekomendasi Perbaikan <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('rekomendasi') is-invalid @enderror" id="rekomendasi" name="rekomendasi" rows="3" required>{{ old('rekomendasi', $temuan->rekomendasi) }}</textarea>
                    @error('rekomendasi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="batas_tindak_lanjut" class="form-label">Batas Waktu Tindak Lanjut</label>
                        <input type="date" class="form-control @error('batas_tindak_lanjut') is-invalid @enderror" id="batas_tindak_lanjut" name="batas_tindak_lanjut" value="{{ old('batas_tindak_lanjut', $temuan->batas_tindak_lanjut?->format('Y-m-d')) }}">
                        @error('batas_tindak_lanjut')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
                <a href="{{ route('admin.ami.temuan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
