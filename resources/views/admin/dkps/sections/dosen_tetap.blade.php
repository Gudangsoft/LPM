@php
    $jabatanLabel = ['tenaga_pengajar' => 'Tenaga Pengajar', 'asisten_ahli' => 'Asisten Ahli', 'lektor' => 'Lektor', 'lektor_kepala' => 'Lektor Kepala', 'guru_besar' => 'Guru Besar'];
@endphp

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDosenModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Dosen Tetap
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr>
                <th>Nama DTPS</th>
                <th>NIDN/NIDK</th>
                <th>Bidang Keahlian</th>
                <th>Jabatan Akademik</th>
                <th>Sertifikat Pendidik</th>
                <th width="90"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($submission->dosenTetap as $row)
            <tr>
                <td>{{ $row->nama }}</td>
                <td>{{ $row->nidn_nidk }}</td>
                <td>{{ $row->bidang_keahlian }}</td>
                <td>{{ $jabatanLabel[$row->jabatan_akademik] ?? '-' }}</td>
                <td>{{ $row->no_sertifikat_pendidik }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editDosenModal"
                        data-id="{{ $row->id }}" data-nama="{{ $row->nama }}" data-nidn="{{ $row->nidn_nidk }}" data-nuptk="{{ $row->nuptk }}"
                        data-magister="{{ $row->pendidikan_magister_bidang }}" data-doktor="{{ $row->pendidikan_doktor_bidang }}"
                        data-bidang="{{ $row->bidang_keahlian }}" data-jabatan="{{ $row->jabatan_akademik }}" data-sertifikat="{{ $row->no_sertifikat_pendidik }}"
                        data-mk-sendiri="{{ $row->mk_diampu_ps_diakreditasi }}" data-mk-lain="{{ $row->mk_diampu_ps_lain }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.dosen.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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

<div class="modal fade" id="addDosenModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.dosen.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Dosen Tetap</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" required></div>
                        <div class="col-md-4 mb-3"><label class="form-label">NIDN/NIDK</label><input type="text" name="nidn_nidk" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">NUPTK</label><input type="text" name="nuptk" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Bidang Keahlian</label><input type="text" name="bidang_keahlian" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Pendidikan Magister (Bidang)</label><input type="text" name="pendidikan_magister_bidang" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Pendidikan Doktor (Bidang)</label><input type="text" name="pendidikan_doktor_bidang" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jabatan Akademik</label>
                            <select name="jabatan_akademik" class="form-select">
                                <option value="">-</option>
                                @foreach($jabatanLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label">No. Sertifikat Pendidik</label><input type="text" name="no_sertifikat_pendidik" class="form-control"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Mata Kuliah Diampu (PS Diakreditasi)</label><textarea name="mk_diampu_ps_diakreditasi" class="form-control" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Mata Kuliah Diampu (PS Lain)</label><textarea name="mk_diampu_ps_lain" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editDosenModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editDosenForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Dosen Tetap</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="nama" id="edit_dosen_nama" class="form-control" required></div>
                        <div class="col-md-4 mb-3"><label class="form-label">NIDN/NIDK</label><input type="text" name="nidn_nidk" id="edit_dosen_nidn" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">NUPTK</label><input type="text" name="nuptk" id="edit_dosen_nuptk" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Bidang Keahlian</label><input type="text" name="bidang_keahlian" id="edit_dosen_bidang" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Pendidikan Magister (Bidang)</label><input type="text" name="pendidikan_magister_bidang" id="edit_dosen_magister" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Pendidikan Doktor (Bidang)</label><input type="text" name="pendidikan_doktor_bidang" id="edit_dosen_doktor" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jabatan Akademik</label>
                            <select name="jabatan_akademik" id="edit_dosen_jabatan" class="form-select">
                                <option value="">-</option>
                                @foreach($jabatanLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label">No. Sertifikat Pendidik</label><input type="text" name="no_sertifikat_pendidik" id="edit_dosen_sertifikat" class="form-control"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Mata Kuliah Diampu (PS Diakreditasi)</label><textarea name="mk_diampu_ps_diakreditasi" id="edit_dosen_mk_sendiri" class="form-control" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Mata Kuliah Diampu (PS Lain)</label><textarea name="mk_diampu_ps_lain" id="edit_dosen_mk_lain" class="form-control" rows="2"></textarea></div>
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
document.getElementById('editDosenModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editDosenForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/dosen") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_dosen_nama').value = btn.getAttribute('data-nama');
    document.getElementById('edit_dosen_nidn').value = btn.getAttribute('data-nidn');
    document.getElementById('edit_dosen_nuptk').value = btn.getAttribute('data-nuptk');
    document.getElementById('edit_dosen_magister').value = btn.getAttribute('data-magister');
    document.getElementById('edit_dosen_doktor').value = btn.getAttribute('data-doktor');
    document.getElementById('edit_dosen_bidang').value = btn.getAttribute('data-bidang');
    document.getElementById('edit_dosen_jabatan').value = btn.getAttribute('data-jabatan') || '';
    document.getElementById('edit_dosen_sertifikat').value = btn.getAttribute('data-sertifikat');
    document.getElementById('edit_dosen_mk_sendiri').value = btn.getAttribute('data-mk-sendiri');
    document.getElementById('edit_dosen_mk_lain').value = btn.getAttribute('data-mk-lain');
});
</script>
@endpush
