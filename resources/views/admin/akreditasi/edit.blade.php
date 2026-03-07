@extends('layouts.admin')

@section('title', 'Edit Akreditasi')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Akreditasi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.akreditasi.index') }}">Akreditasi</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <form action="{{ route('admin.akreditasi.update', $akreditasi) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="prodi_id" class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select class="form-select @error('prodi_id') is-invalid @enderror" id="prodi_id" name="prodi_id" required>
                            <option value="">Pilih Program Studi</option>
                            @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('prodi_id', $akreditasi->prodi_id) == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->jenjang }} - {{ $prodi->nama }}
                            </option>
                            @endforeach
                        </select>
                        @error('prodi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lembaga" class="form-label">Lembaga Akreditasi <span class="text-danger">*</span></label>
                        <select class="form-select @error('lembaga') is-invalid @enderror" id="lembaga" name="lembaga" required>
                            <option value="">Pilih Lembaga</option>
                            @foreach($lembagaOptions as $l)
                            <option value="{{ $l }}" {{ old('lembaga', $akreditasi->lembaga) == $l ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('lembaga')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="peringkat" class="form-label">Peringkat <span class="text-danger">*</span></label>
                        <select class="form-select @error('peringkat') is-invalid @enderror" id="peringkat" name="peringkat" required>
                            <option value="">Pilih Peringkat</option>
                            @foreach($peringkatOptions as $p)
                            <option value="{{ $p }}" {{ old('peringkat', $akreditasi->peringkat) == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                        @error('peringkat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="aktif" {{ old('status', $akreditasi->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="proses_perpanjangan" {{ old('status', $akreditasi->status) == 'proses_perpanjangan' ? 'selected' : '' }}>Proses Perpanjangan</option>
                            <option value="kadaluarsa" {{ old('status', $akreditasi->status) == 'kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="nomor_sk" class="form-label">Nomor SK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nomor_sk') is-invalid @enderror" id="nomor_sk" name="nomor_sk" value="{{ old('nomor_sk', $akreditasi->nomor_sk) }}" required>
                    @error('nomor_sk')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_sk" class="form-label">Tanggal SK <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_sk') is-invalid @enderror" id="tanggal_sk" name="tanggal_sk" value="{{ old('tanggal_sk', $akreditasi->tanggal_sk->format('Y-m-d')) }}" required>
                        @error('tanggal_sk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_kadaluarsa') is-invalid @enderror" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" value="{{ old('tanggal_kadaluarsa', $akreditasi->tanggal_kadaluarsa->format('Y-m-d')) }}" required>
                        @error('tanggal_kadaluarsa')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="file_sk" class="form-label">File SK (PDF)</label>
                    @if($akreditasi->file_sk)
                    <div class="mb-2">
                        <a href="{{ Storage::url($akreditasi->file_sk) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-file-pdf me-1"></i>Lihat File
                        </a>
                    </div>
                    @endif
                    <input type="file" class="form-control @error('file_sk') is-invalid @enderror" id="file_sk" name="file_sk" accept=".pdf">
                    @error('file_sk')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Kosongkan jika tidak ingin mengubah file. Maksimal 5MB</div>
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3">{{ old('catatan', $akreditasi->catatan) }}</textarea>
                    @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
                <a href="{{ route('admin.akreditasi.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
