@php
    $terintegrasiLabel = ['penuh' => 'Terintegrasi Penuh', 'sebagian' => 'Terintegrasi Sebagian', 'tidak_terintegrasi' => 'Tidak Terintegrasi'];
    $mutahirLabel = ['mutahir' => 'Mutahir', 'tidak_mutahir' => 'Tidak Mutahir'];
    $kepemilikanLabel = ['milik_sendiri' => 'Milik Sendiri', 'sewa' => 'Sewa'];
    $kondisiLabel = ['terawat' => 'Terawat', 'tidak_terawat' => 'Tidak Terawat'];
@endphp

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTikModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Infrastruktur/Sistem
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr><th>Nama</th><th>Deskripsi</th><th>Terintegrasi</th><th>Mutahir</th><th>Panduan</th><th>Kepemilikan</th><th>Kondisi</th><th width="90"></th></tr>
        </thead>
        <tbody>
            @forelse($submission->tik as $row)
            <tr>
                <td>{{ $row->nama_infrastruktur }}</td>
                <td class="small">{{ $row->deskripsi }}</td>
                <td>{{ $terintegrasiLabel[$row->terintegrasi] }}</td>
                <td>{{ $mutahirLabel[$row->mutahir] }}</td>
                <td>{{ $row->ada_panduan ? 'Ada' : 'Tidak Ada' }}</td>
                <td>{{ $kepemilikanLabel[$row->kepemilikan] }}</td>
                <td>{{ $kondisiLabel[$row->kondisi] }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editTikModal"
                        data-id="{{ $row->id }}" data-nama="{{ $row->nama_infrastruktur }}" data-deskripsi="{{ $row->deskripsi }}"
                        data-jumlah="{{ $row->jumlah }}" data-terintegrasi="{{ $row->terintegrasi }}" data-mutahir="{{ $row->mutahir }}"
                        data-panduan="{{ $row->ada_panduan ? '1' : '0' }}" data-kepemilikan="{{ $row->kepemilikan }}" data-kondisi="{{ $row->kondisi }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.tik.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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

<div class="modal fade" id="addTikModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.tik.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Infrastruktur TIK</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama Infrastruktur/Sistem Informasi <span class="text-danger">*</span></label><input type="text" name="nama_infrastruktur" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Deskripsi</label><input type="text" name="deskripsi" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Jumlah</label><input type="number" min="0" name="jumlah" class="form-control"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Terintegrasi <span class="text-danger">*</span></label>
                            <select name="terintegrasi" class="form-select" required>
                                @foreach($terintegrasiLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mutahir <span class="text-danger">*</span></label>
                            <select name="mutahir" class="form-select" required>
                                @foreach($mutahirLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
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
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Panduan Tersedia</label>
                            <div class="form-check mt-2"><input type="checkbox" name="ada_panduan" value="1" class="form-check-input" id="add_tik_panduan"><label class="form-check-label" for="add_tik_panduan">Ada</label></div>
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

<div class="modal fade" id="editTikModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editTikForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Infrastruktur TIK</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama Infrastruktur/Sistem Informasi <span class="text-danger">*</span></label><input type="text" name="nama_infrastruktur" id="edit_tik_nama" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Deskripsi</label><input type="text" name="deskripsi" id="edit_tik_deskripsi" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Jumlah</label><input type="number" min="0" name="jumlah" id="edit_tik_jumlah" class="form-control"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Terintegrasi <span class="text-danger">*</span></label>
                            <select name="terintegrasi" id="edit_tik_terintegrasi" class="form-select" required>
                                @foreach($terintegrasiLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mutahir <span class="text-danger">*</span></label>
                            <select name="mutahir" id="edit_tik_mutahir" class="form-select" required>
                                @foreach($mutahirLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kepemilikan <span class="text-danger">*</span></label>
                            <select name="kepemilikan" id="edit_tik_kepemilikan" class="form-select" required>
                                @foreach($kepemilikanLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                            <select name="kondisi" id="edit_tik_kondisi" class="form-select" required>
                                @foreach($kondisiLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Panduan Tersedia</label>
                            <div class="form-check mt-2"><input type="checkbox" name="ada_panduan" value="1" class="form-check-input" id="edit_tik_panduan"><label class="form-check-label" for="edit_tik_panduan">Ada</label></div>
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
document.getElementById('editTikModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editTikForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/tik") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_tik_nama').value = btn.getAttribute('data-nama');
    document.getElementById('edit_tik_deskripsi').value = btn.getAttribute('data-deskripsi');
    document.getElementById('edit_tik_jumlah').value = btn.getAttribute('data-jumlah');
    document.getElementById('edit_tik_terintegrasi').value = btn.getAttribute('data-terintegrasi');
    document.getElementById('edit_tik_mutahir').value = btn.getAttribute('data-mutahir');
    document.getElementById('edit_tik_kepemilikan').value = btn.getAttribute('data-kepemilikan');
    document.getElementById('edit_tik_kondisi').value = btn.getAttribute('data-kondisi');
    document.getElementById('edit_tik_panduan').checked = btn.getAttribute('data-panduan') === '1';
});
</script>
@endpush
