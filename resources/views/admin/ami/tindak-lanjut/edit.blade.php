@extends('layouts.admin')

@section('title', 'Edit Tindak Lanjut')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Tindak Lanjut</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.tindak-lanjut.index') }}">Tindak Lanjut</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <form action="{{ route('admin.ami.tindak-lanjut.update', $tindakLanjut) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Temuan AMI</label>
                    <div class="form-control-plaintext">
                        <span class="badge bg-{{ $tindakLanjut->temuanAmi->kategori_color ?? 'secondary' }} me-1">{{ ucfirst($tindakLanjut->temuanAmi->kategori ?? '-') }}</span>
                        {{ $tindakLanjut->temuanAmi->standar ?? '-' }} - {{ $tindakLanjut->temuanAmi->jadwalAmi->prodi->nama ?? '-' }}
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Tindak Lanjut <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi', $tindakLanjut->deskripsi) }}</textarea>
                    @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal_submit" class="form-label">Tanggal Submit <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal_submit') is-invalid @enderror" id="tanggal_submit" name="tanggal_submit" value="{{ old('tanggal_submit', $tindakLanjut->tanggal_submit?->format('Y-m-d')) }}" required>
                    @error('tanggal_submit')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if($tindakLanjut->file_bukti)
                <div class="mb-3">
                    <label class="form-label">Bukti Saat Ini</label>
                    <div>
                        <a href="{{ Storage::url($tindakLanjut->file_bukti) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-paperclip me-1"></i>Lihat File
                        </a>
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label for="file_bukti" class="form-label">{{ $tindakLanjut->file_bukti ? 'Ganti Bukti Tindak Lanjut' : 'Bukti Tindak Lanjut' }}</label>
                    <input type="file" class="form-control @error('file_bukti') is-invalid @enderror" id="file_bukti" name="file_bukti" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    @error('file_bukti')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Kosongkan jika tidak ingin mengubah. Format: PDF, DOC, DOCX, JPG, PNG. Maks 10MB.</div>
                </div>

                @if($tindakLanjut->catatan_reviewer)
                <div class="alert alert-info">
                    <strong><i class="bi bi-info-circle"></i> Catatan Reviewer:</strong>
                    <p class="mb-0 mt-1">{{ $tindakLanjut->catatan_reviewer }}</p>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
                <a href="{{ route('admin.ami.tindak-lanjut.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
