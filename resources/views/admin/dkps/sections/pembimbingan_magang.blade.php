<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMagangModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Data
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr><th>Nama Dosen</th><th colspan="3" class="text-center">Jml Mahasiswa</th><th colspan="3" class="text-center">Jml Pertemuan</th><th>Lama (Bulan)</th><th width="90"></th></tr>
            <tr><th></th><th>TS-2</th><th>TS-1</th><th>TS</th><th>TS-2</th><th>TS-1</th><th>TS</th><th></th><th></th></tr>
        </thead>
        <tbody>
            @forelse($submission->pembimbinganMagang as $row)
            <tr>
                <td>{{ $row->dosenTetap->nama ?? '-' }}</td>
                <td>{{ $row->jml_mhs_ts2 }}</td><td>{{ $row->jml_mhs_ts1 }}</td><td>{{ $row->jml_mhs_ts }}</td>
                <td>{{ $row->jml_pertemuan_ts2 }}</td><td>{{ $row->jml_pertemuan_ts1 }}</td><td>{{ $row->jml_pertemuan_ts }}</td>
                <td>{{ $row->lama_bulan }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editMagangModal"
                        data-id="{{ $row->id }}" data-dosen="{{ $row->dosen_tetap_id }}" data-a="{{ $row->jml_mhs_ts2 }}" data-b="{{ $row->jml_mhs_ts1 }}" data-c="{{ $row->jml_mhs_ts }}"
                        data-d="{{ $row->jml_pertemuan_ts2 }}" data-e="{{ $row->jml_pertemuan_ts1 }}" data-f="{{ $row->jml_pertemuan_ts }}" data-g="{{ $row->lama_bulan }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.pembimbingan-magang.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="addMagangModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.pembimbingan-magang.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Pembimbingan Magang</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Dosen Pembimbing <span class="text-danger">*</span></label>
                        <select name="dosen_tetap_id" class="form-select" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <label class="form-label small text-muted">Jumlah Mahasiswa Bimbingan</label>
                    <div class="row">
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_mhs_ts2" class="form-control" placeholder="TS-2"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_mhs_ts1" class="form-control" placeholder="TS-1"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_mhs_ts" class="form-control" placeholder="TS"></div>
                    </div>
                    <label class="form-label small text-muted">Jumlah Pertemuan dengan Mahasiswa</label>
                    <div class="row">
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_pertemuan_ts2" class="form-control" placeholder="TS-2"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_pertemuan_ts1" class="form-control" placeholder="TS-1"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_pertemuan_ts" class="form-control" placeholder="TS"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Lama Pelaksanaan Magang (Bulan)</label><input type="number" step="0.1" min="0" name="lama_bulan" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editMagangModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editMagangForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Pembimbingan Magang</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Dosen Pembimbing <span class="text-danger">*</span></label>
                        <select name="dosen_tetap_id" id="edit_magang_dosen" class="form-select" required>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <label class="form-label small text-muted">Jumlah Mahasiswa Bimbingan</label>
                    <div class="row">
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_mhs_ts2" id="edit_magang_a" class="form-control" placeholder="TS-2"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_mhs_ts1" id="edit_magang_b" class="form-control" placeholder="TS-1"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_mhs_ts" id="edit_magang_c" class="form-control" placeholder="TS"></div>
                    </div>
                    <label class="form-label small text-muted">Jumlah Pertemuan dengan Mahasiswa</label>
                    <div class="row">
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_pertemuan_ts2" id="edit_magang_d" class="form-control" placeholder="TS-2"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_pertemuan_ts1" id="edit_magang_e" class="form-control" placeholder="TS-1"></div>
                        <div class="col-md-4 mb-3"><input type="number" min="0" name="jml_pertemuan_ts" id="edit_magang_f" class="form-control" placeholder="TS"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Lama Pelaksanaan Magang (Bulan)</label><input type="number" step="0.1" min="0" name="lama_bulan" id="edit_magang_g" class="form-control"></div>
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
document.getElementById('editMagangModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editMagangForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/pembimbingan-magang") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_magang_dosen').value = btn.getAttribute('data-dosen');
    document.getElementById('edit_magang_a').value = btn.getAttribute('data-a');
    document.getElementById('edit_magang_b').value = btn.getAttribute('data-b');
    document.getElementById('edit_magang_c').value = btn.getAttribute('data-c');
    document.getElementById('edit_magang_d').value = btn.getAttribute('data-d');
    document.getElementById('edit_magang_e').value = btn.getAttribute('data-e');
    document.getElementById('edit_magang_f').value = btn.getAttribute('data-f');
    document.getElementById('edit_magang_g').value = btn.getAttribute('data-g');
});
</script>
@endpush
