@php
    $kualitasLabel = ['sangat_baik' => 'Sangat Baik', 'baik' => 'Baik', 'kurang_baik' => 'Kurang Baik', 'tidak_baik' => 'Tidak Baik'];
    $kepemilikanLabel = ['milik_sendiri' => 'Milik Sendiri', 'sewa' => 'Sewa'];
    $kondisiLabel = ['terawat' => 'Terawat', 'tidak_terawat' => 'Tidak Terawat'];
@endphp

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPrasaranaModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Prasarana
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr><th>Nama Prasarana</th><th>Fungsi</th><th>Jml Unit</th><th>Luas (m2)</th><th>Kualitas</th><th>Kepemilikan</th><th>Kondisi</th><th width="90"></th></tr>
        </thead>
        <tbody>
            @forelse($submission->prasarana as $row)
            <tr>
                <td>{{ $row->nama_prasarana }}</td>
                <td>{{ $row->fungsi }}</td>
                <td>{{ $row->jumlah_unit }}</td>
                <td>{{ $row->total_luas_m2 }}</td>
                <td>{{ $kualitasLabel[$row->kualitas] }}</td>
                <td>{{ $kepemilikanLabel[$row->kepemilikan] }}</td>
                <td>{{ $kondisiLabel[$row->kondisi] }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPrasaranaModal"
                        data-id="{{ $row->id }}" data-nama="{{ $row->nama_prasarana }}" data-fungsi="{{ $row->fungsi }}"
                        data-unit="{{ $row->jumlah_unit }}" data-luas="{{ $row->total_luas_m2 }}" data-kualitas="{{ $row->kualitas }}"
                        data-kepemilikan="{{ $row->kepemilikan }}" data-kondisi="{{ $row->kondisi }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.prasarana.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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

<div class="modal fade" id="addPrasaranaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.prasarana.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Prasarana</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama Prasarana <span class="text-danger">*</span></label><input type="text" name="nama_prasarana" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Fungsi</label><input type="text" name="fungsi" class="form-control"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Jumlah Unit</label><input type="number" min="0" name="jumlah_unit" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Total Luas (m2)</label><input type="number" step="0.01" min="0" name="total_luas_m2" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kualitas <span class="text-danger">*</span></label>
                            <select name="kualitas" class="form-select" required>
                                @foreach($kualitasLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kepemilikan <span class="text-danger">*</span></label>
                            <select name="kepemilikan" class="form-select" required>
                                @foreach($kepemilikanLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                            <select name="kondisi" class="form-select" required>
                                @foreach($kondisiLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
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

<div class="modal fade" id="editPrasaranaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editPrasaranaForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Prasarana</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama Prasarana <span class="text-danger">*</span></label><input type="text" name="nama_prasarana" id="edit_prasarana_nama" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Fungsi</label><input type="text" name="fungsi" id="edit_prasarana_fungsi" class="form-control"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Jumlah Unit</label><input type="number" min="0" name="jumlah_unit" id="edit_prasarana_unit" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Total Luas (m2)</label><input type="number" step="0.01" min="0" name="total_luas_m2" id="edit_prasarana_luas" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kualitas <span class="text-danger">*</span></label>
                            <select name="kualitas" id="edit_prasarana_kualitas" class="form-select" required>
                                @foreach($kualitasLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kepemilikan <span class="text-danger">*</span></label>
                            <select name="kepemilikan" id="edit_prasarana_kepemilikan" class="form-select" required>
                                @foreach($kepemilikanLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                            <select name="kondisi" id="edit_prasarana_kondisi" class="form-select" required>
                                @foreach($kondisiLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
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
document.getElementById('editPrasaranaModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editPrasaranaForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/prasarana") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_prasarana_nama').value = btn.getAttribute('data-nama');
    document.getElementById('edit_prasarana_fungsi').value = btn.getAttribute('data-fungsi');
    document.getElementById('edit_prasarana_unit').value = btn.getAttribute('data-unit');
    document.getElementById('edit_prasarana_luas').value = btn.getAttribute('data-luas');
    document.getElementById('edit_prasarana_kualitas').value = btn.getAttribute('data-kualitas');
    document.getElementById('edit_prasarana_kepemilikan').value = btn.getAttribute('data-kepemilikan');
    document.getElementById('edit_prasarana_kondisi').value = btn.getAttribute('data-kondisi');
});
</script>
@endpush
