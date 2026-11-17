@csrf

<h6 class="text-primary border-bottom pb-2 mb-3"><i class="bi bi-person-vcard me-1"></i>Identitas</h6>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">Kode Dosen</label>
        <input type="text" name="kode_dosen" class="form-control @error('kode_dosen') is-invalid @enderror" value="{{ old('kode_dosen', $dosen->kode_dosen ?? '') }}" placeholder="Kode di SIAKAD">
        @error('kode_dosen')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">NIDN/NIDK</label>
        <input type="text" name="nidn_nidk" class="form-control @error('nidn_nidk') is-invalid @enderror" value="{{ old('nidn_nidk', $dosen->nidn_nidk ?? '') }}" inputmode="numeric">
        @error('nidn_nidk')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">NUPTK</label>
        <input type="text" name="nuptk" class="form-control @error('nuptk') is-invalid @enderror" value="{{ old('nuptk', $dosen->nuptk ?? '') }}" inputmode="numeric">
        @error('nuptk')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Jenis Kelamin</label>
        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
            <option value="">-- Pilih --</option>
            @foreach(\App\Models\Dosen::JENIS_KELAMIN as $key => $label)
            <option value="{{ $key }}" {{ old('jenis_kelamin', $dosen->jenis_kelamin ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">NIP</label>
        <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $dosen->nip ?? '') }}" inputmode="numeric">
        @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Tempat Lahir</label>
        <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $dosen->tempat_lahir ?? '') }}">
        @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', optional($dosen->tanggal_lahir ?? null)->format('Y-m-d')) }}">
        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Agama</label>
        <input type="text" name="agama" list="agamaList" class="form-control @error('agama') is-invalid @enderror" value="{{ old('agama', $dosen->agama ?? '') }}">
        <datalist id="agamaList">@foreach(\App\Models\Dosen::AGAMA as $ag)<option value="{{ $ag }}">@endforeach</datalist>
        @error('agama')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label">Nama Dosen (dengan gelar) <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $dosen->nama ?? '') }}" required>
        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Homebase (Program Studi) <span class="text-danger">*</span></label>
        <select name="prodi_id" class="form-select @error('prodi_id') is-invalid @enderror" required>
            <option value="">-- Pilih Prodi --</option>
            @foreach($prodis as $p)
            <option value="{{ $p->id }}" {{ (string) old('prodi_id', $dosen->prodi_id ?? ($prodis->count() === 1 ? $p->id : '')) === (string) $p->id ? 'selected' : '' }}>{{ $p->jenjang }} {{ $p->nama }}</option>
            @endforeach
        </select>
        @error('prodi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<h6 class="text-primary border-bottom pb-2 mb-3"><i class="bi bi-telephone me-1"></i>Kontak & Alamat</h6>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">No. HP</label>
        <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $dosen->no_hp ?? '') }}" inputmode="tel" placeholder="08xxxxxxxxxx">
        @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $dosen->email ?? '') }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $dosen->alamat ?? '') }}</textarea>
        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<h6 class="text-primary border-bottom pb-2 mb-3"><i class="bi bi-mortarboard me-1"></i>Pendidikan</h6>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">Pendidikan Terakhir</label>
        <select name="pendidikan_terakhir" class="form-select @error('pendidikan_terakhir') is-invalid @enderror">
            <option value="">-- Pilih --</option>
            @foreach(\App\Models\Dosen::PENDIDIKAN as $pd)
            <option value="{{ $pd }}" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir ?? '') === $pd ? 'selected' : '' }}>{{ $pd }}</option>
            @endforeach
        </select>
        @error('pendidikan_terakhir')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-9">
        <label class="form-label">Lulusan (Perguruan Tinggi)</label>
        <input type="text" name="lulusan" class="form-control @error('lulusan') is-invalid @enderror" value="{{ old('lulusan', $dosen->lulusan ?? '') }}" placeholder="Contoh: Universitas Gadjah Mada">
        @error('lulusan')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
    <div class="col-12">
        <label class="form-label">Bidang Keahlian</label>
        <input type="text" name="bidang_keahlian" class="form-control @error('bidang_keahlian') is-invalid @enderror" value="{{ old('bidang_keahlian', $dosen->bidang_keahlian ?? '') }}">
        @error('bidang_keahlian')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<h6 class="text-primary border-bottom pb-2 mb-3"><i class="bi bi-award me-1"></i>Kepegawaian</h6>
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Golongan</label>
        <input type="text" name="golongan" class="form-control @error('golongan') is-invalid @enderror" value="{{ old('golongan', $dosen->golongan ?? '') }}" placeholder="Contoh: III/b">
        @error('golongan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Jabatan Akademik</label>
        <select name="jabatan_akademik" class="form-select @error('jabatan_akademik') is-invalid @enderror">
            <option value="">-- Pilih --</option>
            @foreach(\App\Models\Dosen::JABATAN as $key => $label)
            <option value="{{ $key }}" {{ old('jabatan_akademik', $dosen->jabatan_akademik ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('jabatan_akademik')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-5">
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

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}</button>
    <a href="{{ route('admin.dosen.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
</div>
