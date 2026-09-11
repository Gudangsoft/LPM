@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Standar Mutu <span class="text-danger">*</span></label>
        <select name="standar_mutu_id" class="form-select @error('standar_mutu_id') is-invalid @enderror" required>
            <option value="">-- Pilih Standar --</option>
            @foreach($standarOptions as $s)
            <option value="{{ $s->id }}" {{ (string) old('standar_mutu_id', $sasaranMutu->standar_mutu_id ?? '') === (string) $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
            @endforeach
        </select>
        @error('standar_mutu_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Program Studi</label>
        <select name="prodi_id" class="form-select">
            <option value="">-- Institusional (semua prodi) --</option>
            @foreach($prodiOptions as $p)
            <option value="{{ $p->id }}" {{ (string) old('prodi_id', $sasaranMutu->prodi_id ?? '') === (string) $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
        <div class="form-text">Kosongkan jika sasaran ini berlaku untuk institusi, bukan satu prodi tertentu.</div>
    </div>
    <div class="col-md-2">
        <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
        <input type="text" name="tahun_akademik" class="form-control @error('tahun_akademik') is-invalid @enderror" value="{{ old('tahun_akademik', $sasaranMutu->tahun_akademik ?? '') }}" placeholder="2025/2026" required>
        @error('tahun_akademik')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label">Uraian Sasaran <span class="text-danger">*</span></label>
        <textarea name="uraian_sasaran" rows="2" class="form-control @error('uraian_sasaran') is-invalid @enderror" required>{{ old('uraian_sasaran', $sasaranMutu->uraian_sasaran ?? '') }}</textarea>
        @error('uraian_sasaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Indikator</label>
        <input type="text" name="indikator" class="form-control" value="{{ old('indikator', $sasaranMutu->indikator ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Target</label>
        <input type="text" name="target" class="form-control" value="{{ old('target', $sasaranMutu->target ?? '') }}" placeholder="≥ 3.5">
    </div>
    <div class="col-md-2">
        <label class="form-label">Satuan</label>
        <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $sasaranMutu->satuan ?? '') }}" placeholder="%, poin, dst">
    </div>
    <div class="col-md-3">
        <label class="form-label">Realisasi</label>
        <input type="text" name="realisasi" class="form-control" value="{{ old('realisasi', $sasaranMutu->realisasi ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="belum_dievaluasi" {{ old('status', $sasaranMutu->status ?? 'belum_dievaluasi') === 'belum_dievaluasi' ? 'selected' : '' }}>Belum Dievaluasi</option>
            <option value="tercapai" {{ old('status', $sasaranMutu->status ?? '') === 'tercapai' ? 'selected' : '' }}>Tercapai</option>
            <option value="tidak_tercapai" {{ old('status', $sasaranMutu->status ?? '') === 'tidak_tercapai' ? 'selected' : '' }}>Tidak Tercapai</option>
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label">Keterangan</label>
        <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan', $sasaranMutu->keterangan ?? '') }}">
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}</button>
    <a href="{{ route('admin.ami.sasaran-mutu.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
</div>
