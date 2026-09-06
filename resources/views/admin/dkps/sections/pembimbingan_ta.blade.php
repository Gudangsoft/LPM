<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTaModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Data
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr>
                <th>Nama DTPS</th>
                <th colspan="3" class="text-center">Bimbing (PS Diakreditasi)</th>
                <th colspan="3" class="text-center">Bimbing (PS Lain)</th>
                <th colspan="3" class="text-center">Jml Pertemuan</th>
                <th width="90"></th>
            </tr>
            <tr><th></th><th>TS-2</th><th>TS-1</th><th>TS</th><th>TS-2</th><th>TS-1</th><th>TS</th><th>TS-2</th><th>TS-1</th><th>TS</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($submission->pembimbinganTa as $row)
            <tr>
                <td>{{ $row->dosenTetap->nama ?? '-' }}</td>
                <td>{{ $row->jml_bimbing_ps_sendiri_ts2 }}</td><td>{{ $row->jml_bimbing_ps_sendiri_ts1 }}</td><td>{{ $row->jml_bimbing_ps_sendiri_ts }}</td>
                <td>{{ $row->jml_bimbing_ps_lain_ts2 }}</td><td>{{ $row->jml_bimbing_ps_lain_ts1 }}</td><td>{{ $row->jml_bimbing_ps_lain_ts }}</td>
                <td>{{ $row->jml_pertemuan_ts2 }}</td><td>{{ $row->jml_pertemuan_ts1 }}</td><td>{{ $row->jml_pertemuan_ts }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editTaModal"
                        data-id="{{ $row->id }}" data-dosen="{{ $row->dosen_tetap_id }}"
                        data-a="{{ $row->jml_bimbing_ps_sendiri_ts2 }}" data-b="{{ $row->jml_bimbing_ps_sendiri_ts1 }}" data-c="{{ $row->jml_bimbing_ps_sendiri_ts }}"
                        data-d="{{ $row->jml_bimbing_ps_lain_ts2 }}" data-e="{{ $row->jml_bimbing_ps_lain_ts1 }}" data-f="{{ $row->jml_bimbing_ps_lain_ts }}"
                        data-g="{{ $row->jml_pertemuan_ts2 }}" data-h="{{ $row->jml_pertemuan_ts1 }}" data-i="{{ $row->jml_pertemuan_ts }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.pembimbingan-ta.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="11" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="addTaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.pembimbingan-ta.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Pembimbingan Tugas Akhir</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS <span class="text-danger">*</span></label>
                        <select name="dosen_tetap_id" class="form-select" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <label class="form-label small text-muted">Jumlah Mahasiswa Dibimbing (PS Diakreditasi)</label>
                    <div class="row">
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_bimbing_ps_sendiri_ts2" class="form-control" placeholder="TS-2"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_bimbing_ps_sendiri_ts1" class="form-control" placeholder="TS-1"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_bimbing_ps_sendiri_ts" class="form-control" placeholder="TS"></div>
                    </div>
                    <label class="form-label small text-muted">Jumlah Mahasiswa Dibimbing (PS Lain di PT Sendiri)</label>
                    <div class="row">
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_bimbing_ps_lain_ts2" class="form-control" placeholder="TS-2"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_bimbing_ps_lain_ts1" class="form-control" placeholder="TS-1"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_bimbing_ps_lain_ts" class="form-control" placeholder="TS"></div>
                    </div>
                    <label class="form-label small text-muted">Jumlah Pertemuan Pembimbingan</label>
                    <div class="row">
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_pertemuan_ts2" class="form-control" placeholder="TS-2"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_pertemuan_ts1" class="form-control" placeholder="TS-1"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_pertemuan_ts" class="form-control" placeholder="TS"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editTaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editTaForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Pembimbingan Tugas Akhir</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS <span class="text-danger">*</span></label>
                        <select name="dosen_tetap_id" id="edit_ta_dosen" class="form-select" required>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <label class="form-label small text-muted">Jumlah Mahasiswa Dibimbing (PS Diakreditasi)</label>
                    <div class="row">
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_bimbing_ps_sendiri_ts2" id="edit_ta_a" class="form-control" placeholder="TS-2"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_bimbing_ps_sendiri_ts1" id="edit_ta_b" class="form-control" placeholder="TS-1"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_bimbing_ps_sendiri_ts" id="edit_ta_c" class="form-control" placeholder="TS"></div>
                    </div>
                    <label class="form-label small text-muted">Jumlah Mahasiswa Dibimbing (PS Lain di PT Sendiri)</label>
                    <div class="row">
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_bimbing_ps_lain_ts2" id="edit_ta_d" class="form-control" placeholder="TS-2"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_bimbing_ps_lain_ts1" id="edit_ta_e" class="form-control" placeholder="TS-1"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_bimbing_ps_lain_ts" id="edit_ta_f" class="form-control" placeholder="TS"></div>
                    </div>
                    <label class="form-label small text-muted">Jumlah Pertemuan Pembimbingan</label>
                    <div class="row">
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_pertemuan_ts2" id="edit_ta_g" class="form-control" placeholder="TS-2"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_pertemuan_ts1" id="edit_ta_h" class="form-control" placeholder="TS-1"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_pertemuan_ts" id="edit_ta_i" class="form-control" placeholder="TS"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('editTaModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editTaForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/pembimbingan-ta") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_ta_dosen').value = btn.getAttribute('data-dosen');
    document.getElementById('edit_ta_a').value = btn.getAttribute('data-a');
    document.getElementById('edit_ta_b').value = btn.getAttribute('data-b');
    document.getElementById('edit_ta_c').value = btn.getAttribute('data-c');
    document.getElementById('edit_ta_d').value = btn.getAttribute('data-d');
    document.getElementById('edit_ta_e').value = btn.getAttribute('data-e');
    document.getElementById('edit_ta_f').value = btn.getAttribute('data-f');
    document.getElementById('edit_ta_g').value = btn.getAttribute('data-g');
    document.getElementById('edit_ta_h').value = btn.getAttribute('data-h');
    document.getElementById('edit_ta_i').value = btn.getAttribute('data-i');
});
</script>
@endpush
