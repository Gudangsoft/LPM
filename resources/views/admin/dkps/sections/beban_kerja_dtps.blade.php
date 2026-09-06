<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBebanModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Beban Kerja
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr>
                <th>Nama DTPS</th>
                <th>SKS Pend. PS</th>
                <th>SKS Pend. PS Lain (Dalam PT)</th>
                <th>SKS Pend. PS Lain (Luar PT)</th>
                <th>SKS Penelitian</th>
                <th>SKS PkM</th>
                <th>SKS Tugas Tambahan</th>
                <th>Jumlah SKS</th>
                <th width="90"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($submission->bebanKerjaDtps as $row)
            <tr>
                <td>{{ $row->dosenTetap->nama ?? '-' }}</td>
                <td>{{ $row->sks_pendidikan_ps }}</td>
                <td>{{ $row->sks_pendidikan_ps_lain_dalam }}</td>
                <td>{{ $row->sks_pendidikan_ps_lain_luar }}</td>
                <td>{{ $row->sks_penelitian }}</td>
                <td>{{ $row->sks_pkm }}</td>
                <td>{{ $row->sks_tugas_tambahan }}</td>
                <td><strong>{{ $row->jumlah_sks }}</strong></td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editBebanModal"
                        data-id="{{ $row->id }}" data-dosen="{{ $row->dosen_tetap_id }}" data-a="{{ $row->sks_pendidikan_ps }}"
                        data-b="{{ $row->sks_pendidikan_ps_lain_dalam }}" data-c="{{ $row->sks_pendidikan_ps_lain_luar }}"
                        data-d="{{ $row->sks_penelitian }}" data-e="{{ $row->sks_pkm }}" data-f="{{ $row->sks_tugas_tambahan }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.beban-kerja.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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

@if($submission->dosenTetap->isEmpty())
<p class="text-muted small">Tambahkan data Dosen Tetap (Tabel 6) terlebih dahulu sebelum mengisi beban kerja.</p>
@endif

<div class="modal fade" id="addBebanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.beban-kerja.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Beban Kerja DTPS</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS <span class="text-danger">*</span></label>
                        <select name="dosen_tetap_id" class="form-select" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">SKS Pendidikan (PS Diakreditasi)</label><input type="number" step="0.1" min="0" name="sks_pendidikan_ps" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">SKS Pendidikan (PS Lain, Dalam PT)</label><input type="number" step="0.1" min="0" name="sks_pendidikan_ps_lain_dalam" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">SKS Pendidikan (PS Lain, Luar PT)</label><input type="number" step="0.1" min="0" name="sks_pendidikan_ps_lain_luar" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">SKS Penelitian</label><input type="number" step="0.1" min="0" name="sks_penelitian" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">SKS PkM</label><input type="number" step="0.1" min="0" name="sks_pkm" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">SKS Tugas Tambahan/Penunjang</label><input type="number" step="0.1" min="0" name="sks_tugas_tambahan" class="form-control"></div>
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

<div class="modal fade" id="editBebanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editBebanForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Beban Kerja DTPS</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS <span class="text-danger">*</span></label>
                        <select name="dosen_tetap_id" id="edit_beban_dosen" class="form-select" required>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">SKS Pendidikan (PS Diakreditasi)</label><input type="number" step="0.1" min="0" name="sks_pendidikan_ps" id="edit_beban_a" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">SKS Pendidikan (PS Lain, Dalam PT)</label><input type="number" step="0.1" min="0" name="sks_pendidikan_ps_lain_dalam" id="edit_beban_b" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">SKS Pendidikan (PS Lain, Luar PT)</label><input type="number" step="0.1" min="0" name="sks_pendidikan_ps_lain_luar" id="edit_beban_c" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">SKS Penelitian</label><input type="number" step="0.1" min="0" name="sks_penelitian" id="edit_beban_d" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">SKS PkM</label><input type="number" step="0.1" min="0" name="sks_pkm" id="edit_beban_e" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">SKS Tugas Tambahan/Penunjang</label><input type="number" step="0.1" min="0" name="sks_tugas_tambahan" id="edit_beban_f" class="form-control"></div>
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
document.getElementById('editBebanModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editBebanForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/beban-kerja") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_beban_dosen').value = btn.getAttribute('data-dosen');
    document.getElementById('edit_beban_a').value = btn.getAttribute('data-a');
    document.getElementById('edit_beban_b').value = btn.getAttribute('data-b');
    document.getElementById('edit_beban_c').value = btn.getAttribute('data-c');
    document.getElementById('edit_beban_d').value = btn.getAttribute('data-d');
    document.getElementById('edit_beban_e').value = btn.getAttribute('data-e');
    document.getElementById('edit_beban_f').value = btn.getAttribute('data-f');
});
</script>
@endpush
