@csrf
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Judul <span class="text-danger">*</span></label>
        <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $rtm->judul ?? '') }}" required>
        @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Periode AMI</label>
        <select name="periode_ami_id" class="form-select">
            <option value="">—</option>
            @foreach($periodes as $p)
            <option value="{{ $p->id }}" {{ (string) old('periode_ami_id', $rtm->periode_ami_id ?? '') === (string) $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', optional($rtm->tanggal ?? null)->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Tempat</label>
        <input type="text" name="tempat" class="form-control" value="{{ old('tempat', $rtm->tempat ?? '') }}">
    </div>
    <div class="col-md-5">
        <label class="form-label">Pemimpin Rapat</label>
        <input type="text" name="pemimpin" class="form-control" value="{{ old('pemimpin', $rtm->pemimpin ?? '') }}">
    </div>
    <div class="col-md-5">
        <label class="form-label">Notulen</label>
        <input type="text" name="notulen" class="form-control" value="{{ old('notulen', $rtm->notulen ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="draft" {{ old('status', $rtm->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="selesai" {{ old('status', $rtm->status ?? '') === 'selesai' ? 'selected' : '' }}>Selesai</option>
        </select>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}</button>
    <a href="{{ route('admin.ami.rtm.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
</div>
