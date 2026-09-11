@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Judul Survei <span class="text-danger">*</span></label>
        <input type="text" name="judul_survei" class="form-control @error('judul_survei') is-invalid @enderror" value="{{ old('judul_survei', $surveyKepuasan->judul_survei ?? '') }}" placeholder="Survei Kepuasan Layanan Akademik" required>
        @error('judul_survei')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Jenis Responden <span class="text-danger">*</span></label>
        <select name="jenis_responden" class="form-select @error('jenis_responden') is-invalid @enderror" required>
            @foreach(['mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen', 'tendik' => 'Tenaga Kependidikan', 'alumni' => 'Alumni', 'pengguna_lulusan' => 'Pengguna Lulusan'] as $val => $label)
            <option value="{{ $val }}" {{ old('jenis_responden', $surveyKepuasan->jenis_responden ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('jenis_responden')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Program Studi</label>
        <select name="prodi_id" class="form-select">
            <option value="">-- Institusional --</option>
            @foreach($prodiOptions as $p)
            <option value="{{ $p->id }}" {{ (string) old('prodi_id', $surveyKepuasan->prodi_id ?? '') === (string) $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
        <input type="text" name="tahun_akademik" class="form-control @error('tahun_akademik') is-invalid @enderror" value="{{ old('tahun_akademik', $surveyKepuasan->tahun_akademik ?? '') }}" placeholder="2025/2026" required>
        @error('tahun_akademik')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Semester</label>
        <select name="semester" class="form-select">
            <option value="">-</option>
            <option value="ganjil" {{ old('semester', $surveyKepuasan->semester ?? '') === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
            <option value="genap" {{ old('semester', $surveyKepuasan->semester ?? '') === 'genap' ? 'selected' : '' }}>Genap</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Jumlah Responden <span class="text-danger">*</span></label>
        <input type="number" name="jumlah_responden" min="0" class="form-control @error('jumlah_responden') is-invalid @enderror" value="{{ old('jumlah_responden', $surveyKepuasan->jumlah_responden ?? 0) }}" required>
        @error('jumlah_responden')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Skala Maksimal <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="skala_maksimal" class="form-control @error('skala_maksimal') is-invalid @enderror" value="{{ old('skala_maksimal', $surveyKepuasan->skala_maksimal ?? 4) }}" required>
        @error('skala_maksimal')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Rata-rata Skor</label>
        <input type="number" step="0.01" name="rata_rata_skor" class="form-control" value="{{ old('rata_rata_skor', $surveyKepuasan->rata_rata_skor ?? '') }}">
    </div>
    <div class="col-md-8">
        <label class="form-label">Berkas Laporan Survei</label>
        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".pdf,.doc,.docx,.xls,.xlsx">
        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @isset($surveyKepuasan)
            @if($surveyKepuasan->file_path)
            <small class="text-muted">Berkas saat ini: <a href="{{ Storage::url($surveyKepuasan->file_path) }}" target="_blank" rel="noopener">{{ $surveyKepuasan->file_name }}</a></small>
            @endif
        @endisset
    </div>

    <div class="col-12">
        <label class="form-label">Ringkasan Hasil</label>
        <textarea name="ringkasan_hasil" rows="3" class="form-control">{{ old('ringkasan_hasil', $surveyKepuasan->ringkasan_hasil ?? '') }}</textarea>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}</button>
    <a href="{{ route('admin.ami.survey-kepuasan.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
</div>
