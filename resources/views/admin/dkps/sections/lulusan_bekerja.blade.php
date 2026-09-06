<form action="{{ route('admin.dkps.lulusan-bekerja.update', $submission) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tahun Lulus</th><th width="110">Jml Lulusan</th><th width="110">Jml Terlacak</th>
                    <th width="130">Bekerja Sesuai Bidang</th><th width="120">Usaha Mandiri</th><th width="120">Studi Lanjut S2</th><th width="120">Mengikuti PPG</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submission->lulusanBekerja as $i => $row)
                <tr>
                    <td class="fw-bold">
                        {{ $row->tahun_lulus }}
                        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    @foreach(['jumlah_lulusan','jumlah_terlacak','bekerja_sesuai_bidang','usaha_mandiri','studi_lanjut_s2','mengikuti_ppg'] as $field)
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][{{ $field }}]" value="{{ $row->$field }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
</form>
