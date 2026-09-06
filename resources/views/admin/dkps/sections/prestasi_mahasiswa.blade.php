@php
    $tingkatLabel = ['wilayah_lokal' => 'Wilayah/Lokal', 'nasional' => 'Nasional', 'internasional' => 'Internasional'];
@endphp

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPrestasiModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Prestasi
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr>
                <th>Nama Kegiatan</th>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th>Tingkat</th>
                <th>Prestasi Dicapai</th>
                <th width="90"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($submission->prestasiMahasiswa as $row)
            <tr>
                <td>{{ $row->nama_kegiatan }}</td>
                <td>{{ $row->jenis_prestasi === 'akademik' ? 'Akademik' : 'Non-Akademik' }}</td>
                <td>{{ optional($row->tanggal_perolehan)->format('d/m/Y') }}</td>
                <td>{{ $tingkatLabel[$row->tingkat] }}</td>
                <td>{{ $row->prestasi_dicapai }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPrestasiModal"
                        data-id="{{ $row->id }}" data-nama="{{ $row->nama_kegiatan }}" data-jenis="{{ $row->jenis_prestasi }}"
                        data-tanggal="{{ optional($row->tanggal_perolehan)->format('Y-m-d') }}" data-tingkat="{{ $row->tingkat }}"
                        data-prestasi="{{ $row->prestasi_dicapai }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.prestasi.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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

<div class="modal fade" id="addPrestasiModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.dkps.prestasi.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Prestasi Mahasiswa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kegiatan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Prestasi <span class="text-danger">*</span></label>
                        <select name="jenis_prestasi" class="form-select" required>
                            <option value="akademik">Akademik</option>
                            <option value="non_akademik">Non-Akademik</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Perolehan</label>
                        <input type="date" name="tanggal_perolehan" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                        <select name="tingkat" class="form-select" required>
                            <option value="wilayah_lokal">Wilayah/Lokal</option>
                            <option value="nasional">Nasional</option>
                            <option value="internasional">Internasional</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prestasi yang Dicapai</label>
                        <input type="text" name="prestasi_dicapai" class="form-control">
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

<div class="modal fade" id="editPrestasiModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editPrestasiForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Prestasi Mahasiswa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kegiatan" id="edit_prestasi_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Prestasi <span class="text-danger">*</span></label>
                        <select name="jenis_prestasi" id="edit_prestasi_jenis" class="form-select" required>
                            <option value="akademik">Akademik</option>
                            <option value="non_akademik">Non-Akademik</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Perolehan</label>
                        <input type="date" name="tanggal_perolehan" id="edit_prestasi_tanggal" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                        <select name="tingkat" id="edit_prestasi_tingkat" class="form-select" required>
                            <option value="wilayah_lokal">Wilayah/Lokal</option>
                            <option value="nasional">Nasional</option>
                            <option value="internasional">Internasional</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prestasi yang Dicapai</label>
                        <input type="text" name="prestasi_dicapai" id="edit_prestasi_prestasi" class="form-control">
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
document.getElementById('editPrestasiModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editPrestasiForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/prestasi") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_prestasi_nama').value = btn.getAttribute('data-nama');
    document.getElementById('edit_prestasi_jenis').value = btn.getAttribute('data-jenis');
    document.getElementById('edit_prestasi_tanggal').value = btn.getAttribute('data-tanggal');
    document.getElementById('edit_prestasi_tingkat').value = btn.getAttribute('data-tingkat');
    document.getElementById('edit_prestasi_prestasi').value = btn.getAttribute('data-prestasi');
});
</script>
@endpush
