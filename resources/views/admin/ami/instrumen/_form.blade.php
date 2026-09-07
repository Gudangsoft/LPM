@csrf
<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label class="form-label">Standar Mutu <span class="text-danger">*</span></label>
            <select name="standar_mutu_id" class="form-select @error('standar_mutu_id') is-invalid @enderror" required>
                <option value="">— pilih —</option>
                @foreach($standarMutus as $s)
                <option value="{{ $s->id }}" {{ (string) old('standar_mutu_id', $butir->standar_mutu_id ?? request('standar_mutu_id')) === (string) $s->id ? 'selected' : '' }}>
                    {{ $s->kode ? $s->kode.' — ' : '' }}{{ $s->nama }}
                </option>
                @endforeach
            </select>
            @error('standar_mutu_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Kode Butir</label>
            <input type="text" name="kode" value="{{ old('kode', $butir->kode ?? '') }}" class="form-control @error('kode') is-invalid @enderror" placeholder="1.1">
            @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Pertanyaan Audit <span class="text-danger">*</span></label>
    <textarea name="pertanyaan" rows="2" class="form-control @error('pertanyaan') is-invalid @enderror" required>{{ old('pertanyaan', $butir->pertanyaan ?? '') }}</textarea>
    @error('pertanyaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Indikator / Kriteria Penilaian</label>
    <textarea name="indikator" rows="2" class="form-control @error('indikator') is-invalid @enderror">{{ old('indikator', $butir->indikator ?? '') }}</textarea>
    @error('indikator')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Bobot</label>
            <input type="number" step="0.01" min="0" name="bobot" value="{{ old('bobot', $butir->bobot ?? 1) }}" class="form-control @error('bobot') is-invalid @enderror">
            @error('bobot')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Target</label>
            <input type="text" name="target" value="{{ old('target', $butir->target ?? '') }}" class="form-control" placeholder="&ge; 3.5 / 100%">
        </div>
    </div>
    <div class="col-md-5">
        <div class="mb-3">
            <label class="form-label">Jenis Bukti</label>
            <input type="text" name="jenis_bukti" value="{{ old('jenis_bukti', $butir->jenis_bukti ?? '') }}" class="form-control" placeholder="Dokumen, Wawancara, Observasi">
        </div>
    </div>
</div>

<div class="row align-items-end">
    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">{{ __('admin.order') }}</label>
            <input type="number" min="0" name="urutan" value="{{ old('urutan', $butir->urutan ?? 0) }}" class="form-control">
        </div>
    </div>
    <div class="col-md-9">
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $butir->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}</button>
    <a href="{{ route('admin.ami.instrumen.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>{{ __('admin.back') }}</a>
</div>
