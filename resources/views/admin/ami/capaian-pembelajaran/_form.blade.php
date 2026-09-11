@csrf
<div class="row g-3">
    <div class="col-md-5">
        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
        <select name="prodi_id" class="form-select @error('prodi_id') is-invalid @enderror" required>
            <option value="">-- Pilih Prodi --</option>
            @foreach($prodis as $p)
            <option value="{{ $p->id }}" {{ (string) old('prodi_id', $capaianPembelajaran->prodi_id ?? '') === (string) $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
        @error('prodi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Mata Kuliah <span class="text-danger">*</span></label>
        <input type="text" name="mata_kuliah" class="form-control @error('mata_kuliah') is-invalid @enderror" value="{{ old('mata_kuliah', $capaianPembelajaran->mata_kuliah ?? '') }}" required>
        @error('mata_kuliah')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">CPMK <span class="text-danger">*</span></label>
        <input type="text" name="cpmk" class="form-control @error('cpmk') is-invalid @enderror" value="{{ old('cpmk', $capaianPembelajaran->cpmk ?? '') }}" placeholder="CPMK-1" required>
        @error('cpmk')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label">Deskripsi CPMK</label>
        <textarea name="deskripsi_cpmk" rows="2" class="form-control">{{ old('deskripsi_cpmk', $capaianPembelajaran->deskripsi_cpmk ?? '') }}</textarea>
    </div>

    <div class="col-md-3">
        <label class="form-label">Semester</label>
        <select name="semester" class="form-select">
            <option value="ganjil" {{ old('semester', $capaianPembelajaran->semester ?? 'ganjil') === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
            <option value="genap" {{ old('semester', $capaianPembelajaran->semester ?? '') === 'genap' ? 'selected' : '' }}>Genap</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
        <input type="text" name="tahun_akademik" class="form-control @error('tahun_akademik') is-invalid @enderror" value="{{ old('tahun_akademik', $capaianPembelajaran->tahun_akademik ?? '') }}" placeholder="2025/2026" required>
        @error('tahun_akademik')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Target Capaian (%) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="target_capaian" class="form-control @error('target_capaian') is-invalid @enderror" value="{{ old('target_capaian', $capaianPembelajaran->target_capaian ?? '') }}" required>
        @error('target_capaian')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Realisasi Capaian (%)</label>
        <input type="number" step="0.01" name="realisasi_capaian" class="form-control" value="{{ old('realisasi_capaian', $capaianPembelajaran->realisasi_capaian ?? '') }}">
        <div class="form-text">Kosongkan jika belum ada hasil penilaian.</div>
    </div>

    <div class="col-12">
        <label class="form-label">Catatan</label>
        <textarea name="catatan" rows="2" class="form-control">{{ old('catatan', $capaianPembelajaran->catatan ?? '') }}</textarea>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}</button>
    <a href="{{ route('admin.ami.capaian-pembelajaran.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
</div>
