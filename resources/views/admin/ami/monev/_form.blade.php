@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Program Studi</label>
        <select name="prodi_id" class="form-select">
            <option value="">-- Institusional --</option>
            @foreach($prodiOptions as $p)
            <option value="{{ $p->id }}" {{ (string) old('prodi_id', $monev->prodi_id ?? '') === (string) $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
        <input type="text" name="tahun_akademik" class="form-control @error('tahun_akademik') is-invalid @enderror" value="{{ old('tahun_akademik', $monev->tahun_akademik ?? '') }}" placeholder="2025/2026" required>
        @error('tahun_akademik')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label">Semester</label>
        <select name="semester" class="form-select">
            <option value="ganjil" {{ old('semester', $monev->semester ?? 'ganjil') === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
            <option value="genap" {{ old('semester', $monev->semester ?? '') === 'genap' ? 'selected' : '' }}>Genap</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tanggal Monev <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_monev" class="form-control @error('tanggal_monev') is-invalid @enderror" value="{{ old('tanggal_monev', optional($monev->tanggal_monev ?? null)->format('Y-m-d')) }}" required>
        @error('tanggal_monev')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-8">
        <label class="form-label">Aspek yang Dimonitor <span class="text-danger">*</span></label>
        <input type="text" name="aspek_monev" class="form-control @error('aspek_monev') is-invalid @enderror" value="{{ old('aspek_monev', $monev->aspek_monev ?? '') }}" placeholder="mis. Kehadiran Dosen, Kesesuaian RPS" required>
        @error('aspek_monev')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Petugas Monev</label>
        <input type="text" name="petugas_monev" class="form-control" value="{{ old('petugas_monev', $monev->petugas_monev ?? '') }}">
    </div>

    <div class="col-12">
        <label class="form-label">Hasil <span class="text-danger">*</span></label>
        <textarea name="hasil" rows="3" class="form-control @error('hasil') is-invalid @enderror" required>{{ old('hasil', $monev->hasil ?? '') }}</textarea>
        @error('hasil')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="baik" {{ old('status', $monev->status ?? 'baik') === 'baik' ? 'selected' : '' }}>Baik</option>
            <option value="cukup" {{ old('status', $monev->status ?? '') === 'cukup' ? 'selected' : '' }}>Cukup</option>
            <option value="kurang" {{ old('status', $monev->status ?? '') === 'kurang' ? 'selected' : '' }}>Kurang</option>
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label">Tindak Lanjut</label>
        <input type="text" name="tindak_lanjut" class="form-control" value="{{ old('tindak_lanjut', $monev->tindak_lanjut ?? '') }}">
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}</button>
    <a href="{{ route('admin.ami.monev.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
</div>
