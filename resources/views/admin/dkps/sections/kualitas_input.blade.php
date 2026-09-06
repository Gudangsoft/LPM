<form action="{{ route('admin.dkps.kualitas-input.update', $submission) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tahun Akademik</th>
                    <th>Daya Tampung</th>
                    <th>Pendaftar</th>
                    <th>Lulus Seleksi</th>
                    <th>Mhs Baru Reguler</th>
                    <th>Mhs Baru Transfer</th>
                    <th>Mhs Aktif Reguler</th>
                    <th>Mhs Aktif Transfer</th>
                    <th>Mhs di PDDikti</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submission->kualitasInput as $i => $row)
                <tr>
                    <td class="fw-bold">
                        {{ $row->tahun_relatif }}
                        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    @foreach(['daya_tampung','pendaftar','lulus_seleksi','mahasiswa_baru_reguler','mahasiswa_baru_transfer','mahasiswa_aktif_reguler','mahasiswa_aktif_transfer','mahasiswa_pddikti'] as $field)
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][{{ $field }}]" value="{{ $row->$field }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
</form>
