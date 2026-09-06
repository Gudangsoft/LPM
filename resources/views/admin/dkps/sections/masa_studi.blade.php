<p class="small text-muted">Isi jumlah lulusan pada tahun akademik penuh yang sesuai (kolom yang tidak relevan untuk suatu angkatan boleh dikosongkan).</p>
<form action="{{ route('admin.dkps.masa-studi.update', $submission) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tahun Masuk</th>
                    <th width="110">Jml Diterima</th>
                    <th width="90">TS-7</th><th width="90">TS-6</th><th width="90">TS-5</th><th width="90">TS-4</th>
                    <th width="90">TS-3</th><th width="90">TS-2</th><th width="90">TS-1</th><th width="90">TS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submission->masaStudiLulusan as $i => $row)
                <tr>
                    <td class="fw-bold">
                        {{ $row->tahun_masuk }}
                        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][jumlah_diterima]" value="{{ $row->jumlah_diterima }}"></td>
                    @foreach(['lulus_ts7','lulus_ts6','lulus_ts5','lulus_ts4','lulus_ts3','lulus_ts2','lulus_ts1','lulus_ts'] as $field)
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][{{ $field }}]" value="{{ $row->$field }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
</form>
