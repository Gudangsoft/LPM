<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addKegiatanModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Kegiatan
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr><th>Nama & Tema Kegiatan</th><th>Dosen Pembina</th><th>Tanggal</th><th>Tahun</th><th>Bukti</th><th width="90"></th></tr>
        </thead>
        <tbody>
            @forelse($submission->kegiatanLuarKelas as $row)
            <tr>
                <td>{{ $row->nama_tema_kegiatan }}</td>
                <td>{{ $row->dosen_pembina }}</td>
                <td>{{ optional($row->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $row->tahun_relatif }}</td>
                <td>@if($row->bukti_file)<a href="{{ Storage::url($row->bukti_file) }}" target="_blank"><i class="bi bi-file-earmark-check"></i></a>@else-@endif</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editKegiatanModal"
                        data-id="{{ $row->id }}" data-nama="{{ $row->nama_tema_kegiatan }}" data-pembina="{{ $row->dosen_pembina }}"
                        data-tanggal="{{ optional($row->tanggal)->format('Y-m-d') }}" data-tahun="{{ $row->tahun_relatif }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.kegiatan-luar-kelas.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="addKegiatanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.kegiatan-luar-kelas.store', $submission) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Kegiatan Akademik di Luar Kelas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama dan Tema Kegiatan <span class="text-danger">*</span></label><input type="text" name="nama_tema_kegiatan" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Dosen Pembina</label><input type="text" name="dosen_pembina" class="form-control"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Kegiatan</label><input type="date" name="tanggal" class="form-control"></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun Kegiatan <span class="text-danger">*</span></label>
                            <select name="tahun_relatif" class="form-select" required>
                                <option value="TS-2">TS-2</option><option value="TS-1">TS-1</option><option value="TS">TS</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Bukti Kegiatan</label><input type="file" name="bukti_file" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editKegiatanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editKegiatanForm" action="" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Kegiatan Akademik di Luar Kelas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama dan Tema Kegiatan <span class="text-danger">*</span></label><input type="text" name="nama_tema_kegiatan" id="edit_kegiatan_nama" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Dosen Pembina</label><input type="text" name="dosen_pembina" id="edit_kegiatan_pembina" class="form-control"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Kegiatan</label><input type="date" name="tanggal" id="edit_kegiatan_tanggal" class="form-control"></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun Kegiatan <span class="text-danger">*</span></label>
                            <select name="tahun_relatif" id="edit_kegiatan_tahun" class="form-select" required>
                                <option value="TS-2">TS-2</option><option value="TS-1">TS-1</option><option value="TS">TS</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Ganti Bukti (opsional)</label><input type="file" name="bukti_file" class="form-control"></div>
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
document.getElementById('editKegiatanModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editKegiatanForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/kegiatan-luar-kelas") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_kegiatan_nama').value = btn.getAttribute('data-nama');
    document.getElementById('edit_kegiatan_pembina').value = btn.getAttribute('data-pembina');
    document.getElementById('edit_kegiatan_tanggal').value = btn.getAttribute('data-tanggal');
    document.getElementById('edit_kegiatan_tahun').value = btn.getAttribute('data-tahun');
});
</script>
@endpush
