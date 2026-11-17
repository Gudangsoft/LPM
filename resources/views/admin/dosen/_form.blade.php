@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
        <select name="prodi_id" class="form-select @error('prodi_id') is-invalid @enderror" required>
            <option value="">-- Pilih Prodi --</option>
            @foreach($prodis as $p)
            <option value="{{ $p->id }}" {{ (string) old('prodi_id', $dosen->prodi_id ?? ($prodis->count() === 1 ? $p->id : '')) === (string) $p->id ? 'selected' : '' }}>{{ $p->jenjang }} {{ $p->nama }}</option>
            @endforeach
        </select>
        @error('prodi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Nama Dosen (dengan gelar) <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $dosen->nama ?? '') }}" required>
        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">NIDN/NIDK</label>
        <input type="text" name="nidn_nidk" class="form-control @error('nidn_nidk') is-invalid @enderror" value="{{ old('nidn_nidk', $dosen->nidn_nidk ?? '') }}" inputmode="numeric">
        @error('nidn_nidk')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">NUPTK</label>
        <input type="text" name="nuptk" class="form-control @error('nuptk') is-invalid @enderror" value="{{ old('nuptk', $dosen->nuptk ?? '') }}" inputmode="numeric">
        @error('nuptk')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $dosen->email ?? '') }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Pendidikan Magister/S2 (Bidang)</label>
        <input type="text" name="pendidikan_magister_bidang" class="form-control @error('pendidikan_magister_bidang') is-invalid @enderror" value="{{ old('pendidikan_magister_bidang', $dosen->pendidikan_magister_bidang ?? '') }}">
        @error('pendidikan_magister_bidang')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Pendidikan Doktor/S3 (Bidang)</label>
        <input type="text" name="pendidikan_doktor_bidang" class="form-control @error('pendidikan_doktor_bidang') is-invalid @enderror" value="{{ old('pendidikan_doktor_bidang', $dosen->pendidikan_doktor_bidang ?? '') }}">
        @error('pendidikan_doktor_bidang')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Bidang Keahlian</label>
        <input type="text" name="bidang_keahlian" class="form-control @error('bidang_keahlian') is-invalid @enderror" value="{{ old('bidang_keahlian', $dosen->bidang_keahlian ?? '') }}">
        @error('bidang_keahlian')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Jabatan Akademik</label>
        <select name="jabatan_akademik" class="form-select @error('jabatan_akademik') is-invalid @enderror">
            <option value="">-- Pilih --</option>
            @foreach(\App\Models\Dosen::JABATAN as $key => $label)
            <option value="{{ $key }}" {{ old('jabatan_akademik', $dosen->jabatan_akademik ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('jabatan_akademik')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">No. Sertifikat Pendidik</label>
        <input type="text" name="no_sertifikat_pendidik" class="form-control @error('no_sertifikat_pendidik') is-invalid @enderror" value="{{ old('no_sertifikat_pendidik', $dosen->no_sertifikat_pendidik ?? '') }}">
        @error('no_sertifikat_pendidik')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $dosen->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Dosen aktif</label>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}</button>
    <a href="{{ route('admin.dosen.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
</div>
