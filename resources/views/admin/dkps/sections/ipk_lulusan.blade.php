<form action="{{ route('admin.dkps.ipk-lulusan.update', $submission) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr><th>Tahun Lulus</th><th width="140">Jumlah Lulusan</th><th width="120">IPK Min.</th><th width="120">IPK Rata-rata</th><th width="120">IPK Maks.</th></tr>
            </thead>
            <tbody>
                @foreach($submission->ipkLulusan as $i => $row)
                <tr>
                    <td class="fw-bold">
                        {{ $row->tahun_lulus }}
                        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][jumlah_lulusan]" value="{{ $row->jumlah_lulusan }}"></td>
                    <td><input type="number" step="0.01" min="0" max="4" class="form-control form-control-sm" name="rows[{{ $i }}][ipk_min]" value="{{ $row->ipk_min }}"></td>
                    <td><input type="number" step="0.01" min="0" max="4" class="form-control form-control-sm" name="rows[{{ $i }}][ipk_rata]" value="{{ $row->ipk_rata }}"></td>
                    <td><input type="number" step="0.01" min="0" max="4" class="form-control form-control-sm" name="rows[{{ $i }}][ipk_maks]" value="{{ $row->ipk_maks }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
</form>
