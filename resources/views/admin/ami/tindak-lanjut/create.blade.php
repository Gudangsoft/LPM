@extends('layouts.admin')

@section('title', 'Tambah Tindak Lanjut')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Tambah Tindak Lanjut</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.tindak-lanjut.index') }}">Tindak Lanjut</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <form action="{{ route('admin.ami.tindak-lanjut.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="mb-3">
                    <label for="temuan_ami_id" class="form-label">Temuan AMI <span class="text-danger">*</span></label>
                    <select class="form-select @error('temuan_ami_id') is-invalid @enderror" id="temuan_ami_id" name="temuan_ami_id" required>
                        <option value="">Pilih Temuan</option>
                        @forelse($temuans as $t)
                        <option value="{{ $t->id }}" {{ old('temuan_ami_id', $temuan?->id ?? request('temuan_id')) == $t->id ? 'selected' : '' }}>
                            [{{ ucfirst($t->kategori) }}] {{ Str::limit($t->standar, 30) }} - {{ $t->jadwalAmi->prodi->nama ?? '-' }}
                        </option>
                        @empty
                        <option value="" disabled>Tidak ada temuan terbuka untuk program studi Anda</option>
                        @endforelse
                    </select>
                    @error('temuan_ami_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Tindak Lanjut <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Jelaskan secara detail tindakan yang telah/akan dilakukan untuk menindaklanjuti temuan ini</div>
                </div>

                <div class="mb-3">
                    <label for="tanggal_submit" class="form-label">Tanggal Submit <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal_submit') is-invalid @enderror" id="tanggal_submit" name="tanggal_submit" value="{{ old('tanggal_submit', now()->format('Y-m-d')) }}" required>
                    @error('tanggal_submit')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="file_bukti" class="form-label">Bukti Tindak Lanjut</label>
                    <input type="file" class="form-control @error('file_bukti') is-invalid @enderror" id="file_bukti" name="file_bukti" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    @error('file_bukti')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Format: PDF, DOC, DOCX, JPG, PNG. Maks 10MB.</div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
                <a href="{{ route('admin.ami.tindak-lanjut.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
