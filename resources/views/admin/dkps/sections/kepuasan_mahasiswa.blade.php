@php
    $aspekLabel = ['keandalan' => 'Keandalan', 'daya_tanggap' => 'Daya Tanggap', 'kepastian' => 'Kepastian', 'empati' => 'Empati', 'tangible' => 'Tangible'];
@endphp

<form action="{{ route('admin.dkps.kepuasan-mahasiswa.update', $submission) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Aspek</th>
                    <th width="110">Sangat Baik (%)</th>
                    <th width="110">Baik (%)</th>
                    <th width="110">Cukup (%)</th>
                    <th width="110">Kurang (%)</th>
                    <th>Rencana Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submission->kepuasanMahasiswa as $i => $row)
                <tr>
                    <td class="fw-bold">
                        {{ $aspekLabel[$row->aspek] }}
                        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    <td><input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" name="rows[{{ $i }}][persen_sangat_baik]" value="{{ $row->persen_sangat_baik }}"></td>
                    <td><input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" name="rows[{{ $i }}][persen_baik]" value="{{ $row->persen_baik }}"></td>
                    <td><input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" name="rows[{{ $i }}][persen_cukup]" value="{{ $row->persen_cukup }}"></td>
                    <td><input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" name="rows[{{ $i }}][persen_kurang]" value="{{ $row->persen_kurang }}"></td>
                    <td><input type="text" class="form-control form-control-sm" name="rows[{{ $i }}][rencana_tindak_lanjut]" value="{{ $row->rencana_tindak_lanjut }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
</form>
