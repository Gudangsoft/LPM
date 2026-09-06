@php
    $kemampuanLabel = ['etika' => 'Etika', 'keahlian_bidang_ilmu' => 'Keahlian Bidang Ilmu', 'bahasa_asing' => 'Bahasa Asing', 'ti' => 'Teknologi Informasi',
        'komunikasi' => 'Komunikasi', 'kerjasama_tim' => 'Kerjasama Tim', 'pengembangan_diri' => 'Pengembangan Diri',
        'berfikir_kritis' => 'Berfikir Kritis', 'kreatifitas' => 'Kreatifitas'];
@endphp

<form action="{{ route('admin.dkps.kepuasan-pengguna.update', $submission) }}" method="POST">
    @csrf
    @method('PUT')

    <h6 class="text-muted">Tabel Referensi</h6>
    <div class="table-responsive mb-4">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light"><tr><th>Tahun Lulus</th><th width="150">Jml Lulusan</th><th width="220">Jml Tanggapan Terlacak</th></tr></thead>
            <tbody>
                @foreach($submission->kepuasanPenggunaReferensi as $i => $row)
                <tr>
                    <td class="fw-bold">
                        {{ $row->tahun_lulus }}
                        <input type="hidden" name="referensi[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    <td><input type="number" min="0" class="form-control form-control-sm" name="referensi[{{ $i }}][jumlah_lulusan]" value="{{ $row->jumlah_lulusan }}"></td>
                    <td><input type="number" min="0" class="form-control form-control-sm" name="referensi[{{ $i }}][jumlah_tanggapan_terlacak]" value="{{ $row->jumlah_tanggapan_terlacak }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h6 class="text-muted">Tingkat Kepuasan Pengguna Lulusan per Kemampuan</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr><th>Jenis Kemampuan</th><th width="100">Sangat Baik (%)</th><th width="100">Baik (%)</th><th width="100">Cukup (%)</th><th width="100">Kurang (%)</th><th>Rencana Tindak Lanjut</th></tr>
            </thead>
            <tbody>
                @foreach($submission->kepuasanPenggunaKemampuan as $i => $row)
                <tr>
                    <td class="small">
                        {{ $kemampuanLabel[$row->jenis_kemampuan] }}
                        <input type="hidden" name="kemampuan[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    <td><input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" name="kemampuan[{{ $i }}][persen_sangat_baik]" value="{{ $row->persen_sangat_baik }}"></td>
                    <td><input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" name="kemampuan[{{ $i }}][persen_baik]" value="{{ $row->persen_baik }}"></td>
                    <td><input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" name="kemampuan[{{ $i }}][persen_cukup]" value="{{ $row->persen_cukup }}"></td>
                    <td><input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" name="kemampuan[{{ $i }}][persen_kurang]" value="{{ $row->persen_kurang }}"></td>
                    <td><input type="text" class="form-control form-control-sm" name="kemampuan[{{ $i }}][rencana_tindak_lanjut]" value="{{ $row->rencana_tindak_lanjut }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
</form>
