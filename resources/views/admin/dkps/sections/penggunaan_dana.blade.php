@php
    $kategoriLabel = \App\Models\DkpsPenggunaanDana::KATEGORI;
@endphp

<form action="{{ route('admin.dkps.penggunaan-dana.update', $submission) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kategori</th>
                    <th>Jenis Penggunaan</th>
                    <th colspan="3" class="text-center">Unit Pengelola PS</th>
                    <th colspan="3" class="text-center">Program Studi</th>
                </tr>
                <tr>
                    <th></th><th></th>
                    <th width="100">TS-2</th><th width="100">TS-1</th><th width="100">TS</th>
                    <th width="100">TS-2</th><th width="100">TS-1</th><th width="100">TS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submission->penggunaanDana as $i => $row)
                <tr>
                    <td class="small">
                        {{ $kategoriLabel[$row->kategori] }}{{ $row->sub_item ? ' ('.$row->sub_item.')' : '' }}
                        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    <td><input type="text" class="form-control form-control-sm" name="rows[{{ $i }}][jenis_penggunaan]" value="{{ $row->jenis_penggunaan }}"></td>
                    @foreach(['up_ps_ts2','up_ps_ts1','up_ps_ts','ps_ts2','ps_ts1','ps_ts'] as $field)
                    <td><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][{{ $field }}]" value="{{ $row->$field }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
</form>
