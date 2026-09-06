@php
    $sumberLabel = ['pt_mandiri' => 'Perguruan Tinggi/Mandiri', 'lembaga_dalam_negeri' => 'Lembaga Dalam Negeri (di luar PT)', 'lembaga_luar_negeri' => 'Lembaga Luar Negeri'];
    $rows = $submission->penelitianPkmRingkasan->where('jenis', 'penelitian')->values();
@endphp

<form action="{{ route('admin.dkps.penelitian-ringkasan.update', $submission) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light"><tr><th>Sumber Pembiayaan</th><th width="110">TS-2</th><th width="110">TS-1</th><th width="110">TS</th></tr></thead>
            <tbody>
                @foreach($rows as $i => $row)
                <tr>
                    <td class="small">
                        {{ $sumberLabel[$row->sumber_pembiayaan] }}
                        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][jumlah_ts2]" value="{{ $row->jumlah_ts2 }}"></td>
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][jumlah_ts1]" value="{{ $row->jumlah_ts1 }}"></td>
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][jumlah_ts]" value="{{ $row->jumlah_ts }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
</form>
