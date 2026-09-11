@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Standar Mutu</label>
        <select name="standar_mutu_id" class="form-select">
            <option value="">-- Umum / lintas standar --</option>
            @foreach($standarOptions as $s)
            <option value="{{ $s->id }}" {{ (string) old('standar_mutu_id', $benchmarking->standar_mutu_id ?? '') === (string) $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Program Studi</label>
        <select name="prodi_id" class="form-select">
            <option value="">-- Institusional --</option>
            @foreach($prodiOptions as $p)
            <option value="{{ $p->id }}" {{ (string) old('prodi_id', $benchmarking->prodi_id ?? '') === (string) $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
        <input type="text" name="tahun_akademik" class="form-control @error('tahun_akademik') is-invalid @enderror" value="{{ old('tahun_akademik', $benchmarking->tahun_akademik ?? '') }}" placeholder="2025/2026" required>
        @error('tahun_akademik')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Aspek yang Dibandingkan <span class="text-danger">*</span></label>
        <input type="text" name="aspek" class="form-control @error('aspek') is-invalid @enderror" value="{{ old('aspek', $benchmarking->aspek ?? '') }}" placeholder="mis. Rasio dosen:mahasiswa" required>
        @error('aspek')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Institusi Pembanding <span class="text-danger">*</span></label>
        <input type="text" name="institusi_pembanding" class="form-control @error('institusi_pembanding') is-invalid @enderror" value="{{ old('institusi_pembanding', $benchmarking->institusi_pembanding ?? '') }}" required>
        @error('institusi_pembanding')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Nilai Sendiri</label>
        <input type="text" name="nilai_sendiri" class="form-control" value="{{ old('nilai_sendiri', $benchmarking->nilai_sendiri ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Nilai Pembanding</label>
        <input type="text" name="nilai_pembanding" class="form-control" value="{{ old('nilai_pembanding', $benchmarking->nilai_pembanding ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Kesimpulan</label>
        <textarea name="kesimpulan" rows="3" class="form-control">{{ old('kesimpulan', $benchmarking->kesimpulan ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Rekomendasi</label>
        <textarea name="rekomendasi" rows="3" class="form-control">{{ old('rekomendasi', $benchmarking->rekomendasi ?? '') }}</textarea>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}</button>
    <a href="{{ route('admin.ami.benchmarking.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
</div>
