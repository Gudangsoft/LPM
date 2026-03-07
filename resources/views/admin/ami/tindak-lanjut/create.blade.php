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
        <form action="{{ route('admin.ami.tindak-lanjut.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="temuan_ami_id" class="form-label">Temuan AMI <span class="text-danger">*</span></label>
                        <select class="form-select @error('temuan_ami_id') is-invalid @enderror" id="temuan_ami_id" name="temuan_ami_id" required>
                            <option value="">Pilih Temuan</option>
                            @foreach($temuans as $t)
                            <option value="{{ $t->id }}" {{ old('temuan_ami_id', request('temuan_id')) == $t->id ? 'selected' : '' }}>
                                [{{ ucfirst($t->kategori) }}] {{ Str::limit($t->standar, 30) }} - {{ $t->jadwalAmi->prodi->nama ?? '-' }}
                            </option>
                            @endforeach
                        </select>
                        @error('temuan_ami_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="penanggung_jawab_id" class="form-label">Penanggung Jawab <span class="text-danger">*</span></label>
                        <select class="form-select @error('penanggung_jawab_id') is-invalid @enderror" id="penanggung_jawab_id" name="penanggung_jawab_id" required>
                            <option value="">Pilih Penanggung Jawab</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('penanggung_jawab_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('penanggung_jawab_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="tindakan" class="form-label">Tindakan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('tindakan') is-invalid @enderror" id="tindakan" name="tindakan" value="{{ old('tindakan') }}" placeholder="Judul tindakan yang akan dilakukan" required>
                    @error('tindakan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Jelaskan secara detail tindakan yang akan dilakukan</div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}">
                        @error('tanggal_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}">
                        @error('tanggal_selesai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="diajukan" {{ old('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                            <option value="disetujui" {{ old('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ old('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="bukti_tindak_lanjut" class="form-label">Bukti Tindak Lanjut</label>
                    <textarea class="form-control @error('bukti_tindak_lanjut') is-invalid @enderror" id="bukti_tindak_lanjut" name="bukti_tindak_lanjut" rows="3">{{ old('bukti_tindak_lanjut') }}</textarea>
                    @error('bukti_tindak_lanjut')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Dokumen atau bukti yang menunjukkan tindakan telah dilakukan</div>
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="2">{{ old('catatan') }}</textarea>
                    @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
                <button type="submit" name="submit" value="submit_review" class="btn btn-success">
                    <i class="bi bi-send me-1"></i>Simpan & Ajukan Review
                </button>
                <a href="{{ route('admin.ami.tindak-lanjut.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
