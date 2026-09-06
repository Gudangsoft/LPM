<form action="{{ route('admin.dkps.waktu-tunggu.update', $submission) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tahun Lulus</th><th width="110">Jml Lulusan</th><th width="110">Jml Terlacak</th>
                    <th width="120">WT &lt; 6 Bulan</th><th width="140">6 &le; WT &le; 12 Bulan</th><th width="120">WT &gt; 12 Bulan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submission->waktuTunggu as $i => $row)
                <tr>
                    <td class="fw-bold">
                        {{ $row->tahun_lulus }}
                        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    @foreach(['jumlah_lulusan','jumlah_terlacak','wt_kurang_6_bulan','wt_6_sampai_12_bulan','wt_lebih_12_bulan'] as $field)
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][{{ $field }}]" value="{{ $row->$field }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
</form>
