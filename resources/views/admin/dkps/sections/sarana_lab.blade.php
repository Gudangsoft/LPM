@php
    $kualitasLabel = ['sangat_baik' => 'Sangat Baik', 'baik' => 'Baik', 'kurang_baik' => 'Kurang Baik', 'tidak_baik' => 'Tidak Baik'];
    $kepemilikanLabel = ['milik_sendiri' => 'Milik Sendiri', 'sewa' => 'Sewa'];
    $kondisiLabel = ['terawat' => 'Terawat', 'tidak_terawat' => 'Tidak Terawat'];
@endphp

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSaranaModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Sarana
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr><th>Lab/Ruang</th><th>Alat/Peraga</th><th>Kualitas</th><th>Jumlah</th><th>Kepemilikan</th><th>Kondisi</th><th>Jam/Minggu</th><th width="90"></th></tr>
        </thead>
        <tbody>
            @forelse($submission->saranaLab as $row)
            <tr>
                <td>{{ $row->nama_lab_ruang }}</td>
                <td>{{ $row->nama_alat_peraga }}</td>
                <td>{{ $kualitasLabel[$row->kualitas] }}</td>
                <td>{{ $row->jumlah }}</td>
                <td>{{ $kepemilikanLabel[$row->kepemilikan] }}</td>
                <td>{{ $kondisiLabel[$row->kondisi] }}</td>
                <td>{{ $row->rata_rata_jam_minggu }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSaranaModal"
                        data-id="{{ $row->id }}" data-lab="{{ $row->nama_lab_ruang }}" data-alat="{{ $row->nama_alat_peraga }}"
                        data-kualitas="{{ $row->kualitas }}" data-jumlah="{{ $row->jumlah }}" data-kepemilikan="{{ $row->kepemilikan }}"
                        data-kondisi="{{ $row->kondisi }}" data-jam="{{ $row->rata_rata_jam_minggu }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.sarana-lab.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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

<div class="modal fade" id="addSaranaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.sarana-lab.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Sarana Lab</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Lab/Ruang <span class="text-danger">*</span></label><input type="text" name="nama_lab_ruang" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Alat/Peraga <span class="text-danger">*</span></label><input type="text" name="nama_alat_peraga" class="form-control" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kualitas <span class="text-danger">*</span></label>
                            <select name="kualitas" class="form-select" required>
                                @foreach($kualitasLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3"><label class="form-label">Jumlah</label><input type="number" min="0" name="jumlah" class="form-control"></div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kepemilikan <span class="text-danger">*</span></label>
                            <select name="kepemilikan" class="form-select" required>
                                @foreach($kepemilikanLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                            <select name="kondisi" class="form-select" required>
                                @foreach($kondisiLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Rata-rata Waktu Penggunaan (jam/minggu)</label><input type="number" step="0.1" min="0" name="rata_rata_jam_minggu" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editSaranaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editSaranaForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Sarana Lab</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Lab/Ruang <span class="text-danger">*</span></label><input type="text" name="nama_lab_ruang" id="edit_sarana_lab" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Alat/Peraga <span class="text-danger">*</span></label><input type="text" name="nama_alat_peraga" id="edit_sarana_alat" class="form-control" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kualitas <span class="text-danger">*</span></label>
                            <select name="kualitas" id="edit_sarana_kualitas" class="form-select" required>
                                @foreach($kualitasLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3"><label class="form-label">Jumlah</label><input type="number" min="0" name="jumlah" id="edit_sarana_jumlah" class="form-control"></div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kepemilikan <span class="text-danger">*</span></label>
                            <select name="kepemilikan" id="edit_sarana_kepemilikan" class="form-select" required>
                                @foreach($kepemilikanLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                            <select name="kondisi" id="edit_sarana_kondisi" class="form-select" required>
                                @foreach($kondisiLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Rata-rata Waktu Penggunaan (jam/minggu)</label><input type="number" step="0.1" min="0" name="rata_rata_jam_minggu" id="edit_sarana_jam" class="form-control"></div>
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
document.getElementById('editSaranaModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editSaranaForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/sarana-lab") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_sarana_lab').value = btn.getAttribute('data-lab');
    document.getElementById('edit_sarana_alat').value = btn.getAttribute('data-alat');
    document.getElementById('edit_sarana_kualitas').value = btn.getAttribute('data-kualitas');
    document.getElementById('edit_sarana_jumlah').value = btn.getAttribute('data-jumlah');
    document.getElementById('edit_sarana_kepemilikan').value = btn.getAttribute('data-kepemilikan');
    document.getElementById('edit_sarana_kondisi').value = btn.getAttribute('data-kondisi');
    document.getElementById('edit_sarana_jam').value = btn.getAttribute('data-jam');
});
</script>
@endpush
