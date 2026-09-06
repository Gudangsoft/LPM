<form action="{{ route('admin.dkps.kesesuaian-bidang.update', $submission) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr><th>Tahun Lulus</th><th width="110">Jml Lulusan</th><th width="110">Jml Terlacak</th><th width="110">Rendah</th><th width="110">Sedang</th><th width="110">Tinggi</th></tr>
            </thead>
            <tbody>
                @foreach($submission->kesesuaianBidang as $i => $row)
                <tr>
                    <td class="fw-bold">
                        {{ $row->tahun_lulus }}
                        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    @foreach(['jumlah_lulusan','jumlah_terlacak','rendah','sedang','tinggi'] as $field)
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][{{ $field }}]" value="{{ $row->$field }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
</form>
