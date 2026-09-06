@php
    $jenisLabel = ['visiting_lecturer' => 'Visiting Lecturer/Scholar', 'keynote_speaker' => 'Keynote/Invited Speaker', 'editor_mitra_bestari' => 'Editor/Mitra Bestari', 'staf_ahli_narasumber' => 'Staf Ahli/Narasumber', 'penghargaan_prestasi' => 'Penghargaan Prestasi & Kinerja'];
    $tingkatLabel = ['wilayah_lokal' => 'Wilayah/Lokal', 'nasional' => 'Nasional', 'internasional' => 'Internasional'];
@endphp

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addRekognisiModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Rekognisi
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr>
                <th>Nama DTPS</th>
                <th>Deskripsi</th>
                <th>Jenis</th>
                <th>Tahun</th>
                <th>Tingkat</th>
                <th>Bukti</th>
                <th width="90"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($submission->rekognisiDtps as $row)
            <tr>
                <td>{{ $row->dosenTetap->nama ?? '-' }}</td>
                <td>{{ $row->deskripsi_rekognisi }}</td>
                <td>{{ $jenisLabel[$row->jenis_rekognisi] }}</td>
                <td>{{ $row->tahun }}</td>
                <td>{{ $tingkatLabel[$row->tingkat] }}</td>
                <td>@if($row->bukti_file)<a href="{{ Storage::url($row->bukti_file) }}" target="_blank"><i class="bi bi-file-earmark-check"></i></a>@else-@endif</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editRekognisiModal"
                        data-id="{{ $row->id }}" data-dosen="{{ $row->dosen_tetap_id }}" data-bidang="{{ $row->bidang_keahlian }}"
                        data-deskripsi="{{ $row->deskripsi_rekognisi }}" data-jenis="{{ $row->jenis_rekognisi }}" data-tahun="{{ $row->tahun }}" data-tingkat="{{ $row->tingkat }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.rekognisi.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="addRekognisiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.rekognisi.store', $submission) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Rekognisi DTPS</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS <span class="text-danger">*</span></label>
                        <select name="dosen_tetap_id" class="form-select" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Bidang Keahlian</label><input type="text" name="bidang_keahlian" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Deskripsi Rekognisi <span class="text-danger">*</span></label><input type="text" name="deskripsi_rekognisi" class="form-control" required></div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Rekognisi <span class="text-danger">*</span></label>
                        <select name="jenis_rekognisi" class="form-select" required>
                            @foreach($jenisLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Tahun</label><input type="number" name="tahun" class="form-control" min="2000" max="2100"></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                            <select name="tingkat" class="form-select" required>
                                @foreach($tingkatLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Bukti Pendukung</label><input type="file" name="bukti_file" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editRekognisiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editRekognisiForm" action="" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Rekognisi DTPS</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS <span class="text-danger">*</span></label>
                        <select name="dosen_tetap_id" id="edit_rekognisi_dosen" class="form-select" required>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Bidang Keahlian</label><input type="text" name="bidang_keahlian" id="edit_rekognisi_bidang" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Deskripsi Rekognisi <span class="text-danger">*</span></label><input type="text" name="deskripsi_rekognisi" id="edit_rekognisi_deskripsi" class="form-control" required></div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Rekognisi <span class="text-danger">*</span></label>
                        <select name="jenis_rekognisi" id="edit_rekognisi_jenis" class="form-select" required>
                            @foreach($jenisLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Tahun</label><input type="number" name="tahun" id="edit_rekognisi_tahun" class="form-control" min="2000" max="2100"></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                            <select name="tingkat" id="edit_rekognisi_tingkat" class="form-select" required>
                                @foreach($tingkatLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Ganti Bukti Pendukung (opsional)</label><input type="file" name="bukti_file" class="form-control"></div>
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
document.getElementById('editRekognisiModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editRekognisiForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/rekognisi") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_rekognisi_dosen').value = btn.getAttribute('data-dosen');
    document.getElementById('edit_rekognisi_bidang').value = btn.getAttribute('data-bidang');
    document.getElementById('edit_rekognisi_deskripsi').value = btn.getAttribute('data-deskripsi');
    document.getElementById('edit_rekognisi_jenis').value = btn.getAttribute('data-jenis');
    document.getElementById('edit_rekognisi_tahun').value = btn.getAttribute('data-tahun');
    document.getElementById('edit_rekognisi_tingkat').value = btn.getAttribute('data-tingkat');
});
</script>
@endpush
