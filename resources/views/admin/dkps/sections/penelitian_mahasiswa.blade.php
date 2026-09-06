@php $rows = $submission->penelitianPkmMahasiswa->where('jenis', 'penelitian'); @endphp

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPenelitianMhsModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Data
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light"><tr><th>Nama DTPS</th><th>Judul/Tema</th><th>NIM & Nama Mahasiswa</th><th>Peran Mahasiswa</th><th>Tahun</th><th width="90"></th></tr></thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row->nama_dtps }}</td>
                <td>{{ $row->judul_tema }}</td>
                <td class="small" style="white-space: pre-line;">{{ $row->nim_nama_mahasiswa }}</td>
                <td>{{ $row->peran_mahasiswa }}</td>
                <td>{{ $row->tahun_relatif }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPenelitianMhsModal"
                        data-id="{{ $row->id }}" data-dtps="{{ $row->nama_dtps }}" data-judul="{{ $row->judul_tema }}"
                        data-mhs="{{ $row->nim_nama_mahasiswa }}" data-peran="{{ $row->peran_mahasiswa }}" data-tahun="{{ $row->tahun_relatif }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.penelitian-mahasiswa.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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

<div class="modal fade" id="addPenelitianMhsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.penelitian-mahasiswa.store', $submission) }}" method="POST">
            @csrf
            <input type="hidden" name="jenis" value="penelitian">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Penelitian Melibatkan Mahasiswa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama DTPS</label><input type="text" name="nama_dtps" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Judul/Tema Penelitian sesuai Roadmap <span class="text-danger">*</span></label><input type="text" name="judul_tema" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">NIM dan Nama Mahasiswa</label><textarea name="nim_nama_mahasiswa" class="form-control" rows="3" placeholder="Satu mahasiswa per baris"></textarea></div>
                    <div class="mb-3"><label class="form-label">Peran Mahasiswa</label><input type="text" name="peran_mahasiswa" class="form-control"></div>
                    <div class="mb-3">
                        <label class="form-label">Tahun Penelitian <span class="text-danger">*</span></label>
                        <select name="tahun_relatif" class="form-select" required>
                            <option value="TS-2">TS-2</option><option value="TS-1">TS-1</option><option value="TS">TS</option>
                        </select>
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

<div class="modal fade" id="editPenelitianMhsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editPenelitianMhsForm" action="" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="jenis" value="penelitian">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Penelitian Melibatkan Mahasiswa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama DTPS</label><input type="text" name="nama_dtps" id="edit_pm_dtps" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Judul/Tema Penelitian sesuai Roadmap <span class="text-danger">*</span></label><input type="text" name="judul_tema" id="edit_pm_judul" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">NIM dan Nama Mahasiswa</label><textarea name="nim_nama_mahasiswa" id="edit_pm_mhs" class="form-control" rows="3"></textarea></div>
                    <div class="mb-3"><label class="form-label">Peran Mahasiswa</label><input type="text" name="peran_mahasiswa" id="edit_pm_peran" class="form-control"></div>
                    <div class="mb-3">
                        <label class="form-label">Tahun Penelitian <span class="text-danger">*</span></label>
                        <select name="tahun_relatif" id="edit_pm_tahun" class="form-select" required>
                            <option value="TS-2">TS-2</option><option value="TS-1">TS-1</option><option value="TS">TS</option>
                        </select>
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
document.getElementById('editPenelitianMhsModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editPenelitianMhsForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/penelitian-mahasiswa") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_pm_dtps').value = btn.getAttribute('data-dtps');
    document.getElementById('edit_pm_judul').value = btn.getAttribute('data-judul');
    document.getElementById('edit_pm_mhs').value = btn.getAttribute('data-mhs');
    document.getElementById('edit_pm_peran').value = btn.getAttribute('data-peran');
    document.getElementById('edit_pm_tahun').value = btn.getAttribute('data-tahun');
});
</script>
@endpush
