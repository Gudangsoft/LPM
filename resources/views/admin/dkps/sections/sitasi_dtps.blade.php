<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSitasiModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Sitasi
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light"><tr><th>Nama DTPS</th><th>Judul Karya yang Disitasi</th><th>Jumlah Sitasi</th><th width="90"></th></tr></thead>
        <tbody>
            @forelse($submission->sitasiDtps as $row)
            <tr>
                <td>{{ $row->dosenTetap->nama ?? '-' }}</td>
                <td class="small">{{ Str::limit($row->judul_karya_disitasi, 80) }}</td>
                <td>{{ $row->jumlah_sitasi }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSitasiModal"
                        data-id="{{ $row->id }}" data-dosen="{{ $row->dosen_tetap_id }}" data-judul="{{ $row->judul_karya_disitasi }}" data-jumlah="{{ $row->jumlah_sitasi }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.sitasi-dtps.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="addSitasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.sitasi-dtps.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Karya yang Disitasi</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS</label>
                        <select name="dosen_tetap_id" class="form-select">
                            <option value="">-</option>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Judul Karya (Jurnal/Buku, Volume, Tahun, Nomor, Halaman) <span class="text-danger">*</span></label><textarea name="judul_karya_disitasi" class="form-control" rows="2" required></textarea></div>
                    <div class="mb-3"><label class="form-label">Jumlah Sitasi</label><input type="number" min="0" name="jumlah_sitasi" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editSitasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editSitasiForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Karya yang Disitasi</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS</label>
                        <select name="dosen_tetap_id" id="edit_sitasi_dosen" class="form-select">
                            <option value="">-</option>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Judul Karya (Jurnal/Buku, Volume, Tahun, Nomor, Halaman) <span class="text-danger">*</span></label><textarea name="judul_karya_disitasi" id="edit_sitasi_judul" class="form-control" rows="2" required></textarea></div>
                    <div class="mb-3"><label class="form-label">Jumlah Sitasi</label><input type="number" min="0" name="jumlah_sitasi" id="edit_sitasi_jumlah" class="form-control"></div>
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
document.getElementById('editSitasiModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editSitasiForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/sitasi-dtps") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_sitasi_dosen').value = btn.getAttribute('data-dosen') || '';
    document.getElementById('edit_sitasi_judul').value = btn.getAttribute('data-judul');
    document.getElementById('edit_sitasi_jumlah').value = btn.getAttribute('data-jumlah');
});
</script>
@endpush
