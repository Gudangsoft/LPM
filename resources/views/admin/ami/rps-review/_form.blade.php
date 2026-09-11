@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
        <select name="prodi_id" class="form-select @error('prodi_id') is-invalid @enderror" required>
            <option value="">-- Pilih Prodi --</option>
            @foreach($prodis as $p)
            <option value="{{ $p->id }}" {{ (string) old('prodi_id', $rpsReview->prodi_id ?? '') === (string) $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
        @error('prodi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Kode MK</label>
        <input type="text" name="kode_mk" class="form-control" value="{{ old('kode_mk', $rpsReview->kode_mk ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">SKS</label>
        <input type="number" name="sks" min="1" max="12" class="form-control" value="{{ old('sks', $rpsReview->sks ?? '') }}">
    </div>

    <div class="col-md-8">
        <label class="form-label">Mata Kuliah <span class="text-danger">*</span></label>
        <input type="text" name="mata_kuliah" class="form-control @error('mata_kuliah') is-invalid @enderror" value="{{ old('mata_kuliah', $rpsReview->mata_kuliah ?? '') }}" required>
        @error('mata_kuliah')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Dosen Pengampu <span class="text-danger">*</span></label>
        <input type="text" name="dosen_pengampu" class="form-control @error('dosen_pengampu') is-invalid @enderror" value="{{ old('dosen_pengampu', $rpsReview->dosen_pengampu ?? '') }}" required>
        @error('dosen_pengampu')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Semester</label>
        <select name="semester" class="form-select">
            <option value="ganjil" {{ old('semester', $rpsReview->semester ?? 'ganjil') === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
            <option value="genap" {{ old('semester', $rpsReview->semester ?? '') === 'genap' ? 'selected' : '' }}>Genap</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
        <input type="text" name="tahun_akademik" class="form-control @error('tahun_akademik') is-invalid @enderror" value="{{ old('tahun_akademik', $rpsReview->tahun_akademik ?? '') }}" placeholder="2025/2026" required>
        @error('tahun_akademik')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Berkas RPS</label>
        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".pdf,.doc,.docx">
        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @isset($rpsReview)
            @if($rpsReview->file_path)
            <small class="text-muted">Berkas saat ini: <a href="{{ Storage::url($rpsReview->file_path) }}" target="_blank" rel="noopener">{{ $rpsReview->file_name }}</a>. Unggah berkas baru untuk mengganti.</small>
            @endif
        @endisset
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}</button>
    <a href="{{ route('admin.ami.rps-review.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
</div>
