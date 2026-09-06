@php $rows = $submission->pengembanganKompetensi->where('person_type', 'dosen'); @endphp

<p class="small text-muted">Minimal 32 JP (jam pelajaran) per tahun untuk setiap DTPS.</p>

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPengembanganDosenModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Kegiatan
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr><th>Nama DTPS</th><th>Deskripsi Kegiatan</th><th>Tempat</th><th>Waktu</th><th>Bukti</th><th width="90"></th></tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row->dosenTetap->nama ?? '-' }}</td>
                <td>{{ $row->deskripsi_kegiatan }}</td>
                <td>{{ $row->tempat }}</td>
                <td>{{ $row->waktu_pelaksanaan }}</td>
                <td>@if($row->bukti_file)<a href="{{ Storage::url($row->bukti_file) }}" target="_blank"><i class="bi bi-file-earmark-check"></i></a>@else-@endif</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPengembanganDosenModal"
                        data-id="{{ $row->id }}" data-dosen="{{ $row->dosen_tetap_id }}" data-deskripsi="{{ $row->deskripsi_kegiatan }}"
                        data-tempat="{{ $row->tempat }}" data-waktu="{{ $row->waktu_pelaksanaan }}" data-manfaat="{{ $row->manfaat }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.pengembangan.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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

<div class="modal fade" id="addPengembanganDosenModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.pengembangan.store', $submission) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="person_type" value="dosen">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Pengembangan Kompetensi DTPS</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS <span class="text-danger">*</span></label>
                        <select name="dosen_tetap_id" class="form-select" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Deskripsi Kegiatan <span class="text-danger">*</span></label><input type="text" name="deskripsi_kegiatan" class="form-control" required></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Tempat</label><input type="text" name="tempat" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Waktu Pelaksanaan</label><input type="text" name="waktu_pelaksanaan" class="form-control" placeholder="mis. 10 Februari 2025"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Manfaat Kegiatan</label><textarea name="manfaat" class="form-control" rows="2"></textarea></div>
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

<div class="modal fade" id="editPengembanganDosenModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editPengembanganDosenForm" action="" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <input type="hidden" name="person_type" value="dosen">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Pengembangan Kompetensi DTPS</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS <span class="text-danger">*</span></label>
                        <select name="dosen_tetap_id" id="edit_pengembangan_dosen_dosen" class="form-select" required>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Deskripsi Kegiatan <span class="text-danger">*</span></label><input type="text" name="deskripsi_kegiatan" id="edit_pengembangan_dosen_deskripsi" class="form-control" required></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Tempat</label><input type="text" name="tempat" id="edit_pengembangan_dosen_tempat" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Waktu Pelaksanaan</label><input type="text" name="waktu_pelaksanaan" id="edit_pengembangan_dosen_waktu" class="form-control"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Manfaat Kegiatan</label><textarea name="manfaat" id="edit_pengembangan_dosen_manfaat" class="form-control" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Ganti Bukti Kegiatan (opsional)</label><input type="file" name="bukti_file" class="form-control"></div>
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
document.getElementById('editPengembanganDosenModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editPengembanganDosenForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/pengembangan") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_pengembangan_dosen_dosen').value = btn.getAttribute('data-dosen');
    document.getElementById('edit_pengembangan_dosen_deskripsi').value = btn.getAttribute('data-deskripsi');
    document.getElementById('edit_pengembangan_dosen_tempat').value = btn.getAttribute('data-tempat');
    document.getElementById('edit_pengembangan_dosen_waktu').value = btn.getAttribute('data-waktu');
    document.getElementById('edit_pengembangan_dosen_manfaat').value = btn.getAttribute('data-manfaat');
});
</script>
@endpush
