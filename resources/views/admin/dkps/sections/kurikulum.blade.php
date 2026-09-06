@php $semesters = \App\Models\DkpsKurikulum::SEMESTER; @endphp

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addKurikulumModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Mata Kuliah
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr><th>Smt</th><th>Kode MK</th><th>Nama MK</th><th>Inti PS</th><th>SKS Kuliah</th><th>SKS Praktikum</th><th>SKS Praktik Lapangan</th><th width="90"></th></tr>
        </thead>
        <tbody>
            @forelse($submission->kurikulum as $row)
            <tr>
                <td>{{ $row->semester }}</td>
                <td>{{ $row->kode_mk }}</td>
                <td>{{ $row->nama_mk }}</td>
                <td>{{ $row->kompetensi_inti ? 'Ya' : '-' }}</td>
                <td>{{ $row->sks_kuliah }}</td>
                <td>{{ $row->sks_praktikum }}</td>
                <td>{{ $row->sks_praktik_lapangan }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editKurikulumModal"
                        data-id="{{ $row->id }}" data-semester="{{ $row->semester }}" data-kode="{{ $row->kode_mk }}" data-nama="{{ $row->nama_mk }}"
                        data-inti="{{ $row->kompetensi_inti ? '1' : '0' }}" data-kuliah="{{ $row->sks_kuliah }}" data-praktikum="{{ $row->sks_praktikum }}"
                        data-lapangan="{{ $row->sks_praktik_lapangan }}" data-rps="{{ $row->tautan_rps }}" data-cpl="{{ $row->tautan_asesmen_cpl }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.kurikulum.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="addKurikulumModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.kurikulum.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Mata Kuliah</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Semester <span class="text-danger">*</span></label>
                            <select name="semester" class="form-select" required>
                                @foreach($semesters as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3"><label class="form-label">Kode MK</label><input type="text" name="kode_mk" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama MK <span class="text-danger">*</span></label><input type="text" name="nama_mk" class="form-control" required></div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="kompetensi_inti" value="1" class="form-check-input" id="add_kurikulum_inti">
                        <label class="form-check-label" for="add_kurikulum_inti">Mata Kuliah Kompetensi Inti/Penciri PS</label>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">SKS Kuliah</label><input type="number" step="0.1" min="0" name="sks_kuliah" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">SKS Praktikum</label><input type="number" step="0.1" min="0" name="sks_praktikum" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">SKS Praktik Lapangan</label><input type="number" step="0.1" min="0" name="sks_praktik_lapangan" class="form-control"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Tautan RPS</label><input type="text" name="tautan_rps" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Tautan Asesmen CPL</label><input type="text" name="tautan_asesmen_cpl" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editKurikulumModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editKurikulumForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Mata Kuliah</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Semester <span class="text-danger">*</span></label>
                            <select name="semester" id="edit_kurikulum_semester" class="form-select" required>
                                @foreach($semesters as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3"><label class="form-label">Kode MK</label><input type="text" name="kode_mk" id="edit_kurikulum_kode" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama MK <span class="text-danger">*</span></label><input type="text" name="nama_mk" id="edit_kurikulum_nama" class="form-control" required></div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="kompetensi_inti" value="1" class="form-check-input" id="edit_kurikulum_inti">
                        <label class="form-check-label" for="edit_kurikulum_inti">Mata Kuliah Kompetensi Inti/Penciri PS</label>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">SKS Kuliah</label><input type="number" step="0.1" min="0" name="sks_kuliah" id="edit_kurikulum_kuliah" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">SKS Praktikum</label><input type="number" step="0.1" min="0" name="sks_praktikum" id="edit_kurikulum_praktikum" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">SKS Praktik Lapangan</label><input type="number" step="0.1" min="0" name="sks_praktik_lapangan" id="edit_kurikulum_lapangan" class="form-control"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Tautan RPS</label><input type="text" name="tautan_rps" id="edit_kurikulum_rps" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Tautan Asesmen CPL</label><input type="text" name="tautan_asesmen_cpl" id="edit_kurikulum_cpl" class="form-control"></div>
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
document.getElementById('editKurikulumModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editKurikulumForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/kurikulum") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_kurikulum_semester').value = btn.getAttribute('data-semester');
    document.getElementById('edit_kurikulum_kode').value = btn.getAttribute('data-kode');
    document.getElementById('edit_kurikulum_nama').value = btn.getAttribute('data-nama');
    document.getElementById('edit_kurikulum_inti').checked = btn.getAttribute('data-inti') === '1';
    document.getElementById('edit_kurikulum_kuliah').value = btn.getAttribute('data-kuliah');
    document.getElementById('edit_kurikulum_praktikum').value = btn.getAttribute('data-praktikum');
    document.getElementById('edit_kurikulum_lapangan').value = btn.getAttribute('data-lapangan');
    document.getElementById('edit_kurikulum_rps').value = btn.getAttribute('data-rps');
    document.getElementById('edit_kurikulum_cpl').value = btn.getAttribute('data-cpl');
});
</script>
@endpush
