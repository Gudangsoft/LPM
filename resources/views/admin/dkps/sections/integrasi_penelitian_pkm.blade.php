@php
    $bentukLabel = ['tambahan_materi' => 'Tambahan Materi Perkuliahan', 'studi_kasus' => 'Studi Kasus', 'bab_buku_ajar' => 'Bab/Subab dalam Buku Ajar', 'bahan_ajar' => 'Bahan Ajar', 'bentuk_lain' => 'Bentuk Lain yang Relevan'];
@endphp

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addIntegrasiModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Integrasi
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr><th>Nama DTPS</th><th>Judul Penelitian/PkM</th><th>Mata Kuliah</th><th>Bentuk Integrasi</th><th>Tahun</th><th>Bukti</th><th width="90"></th></tr>
        </thead>
        <tbody>
            @forelse($submission->integrasiPenelitianPkm as $row)
            <tr>
                <td>{{ $row->dosenTetap->nama ?? '-' }}</td>
                <td>{{ $row->judul_penelitian_pkm }}</td>
                <td>{{ $row->mata_kuliah }}</td>
                <td class="small">{{ $bentukLabel[$row->bentuk_integrasi] }}</td>
                <td>{{ $row->tahun_relatif }}</td>
                <td>@if($row->bukti_file)<a href="{{ Storage::url($row->bukti_file) }}" target="_blank"><i class="bi bi-file-earmark-check"></i></a>@else-@endif</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editIntegrasiModal"
                        data-id="{{ $row->id }}" data-dosen="{{ $row->dosen_tetap_id }}" data-judul="{{ $row->judul_penelitian_pkm }}"
                        data-mk="{{ $row->mata_kuliah }}" data-bentuk="{{ $row->bentuk_integrasi }}" data-tahun="{{ $row->tahun_relatif }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.integrasi.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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

<div class="modal fade" id="addIntegrasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.integrasi.store', $submission) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Integrasi Penelitian/PkM</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS</label>
                        <select name="dosen_tetap_id" class="form-select">
                            <option value="">-</option>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Judul Penelitian/PkM <span class="text-danger">*</span></label><input type="text" name="judul_penelitian_pkm" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Mata Kuliah Hasil Integrasi</label><input type="text" name="mata_kuliah" class="form-control"></div>
                    <div class="mb-3">
                        <label class="form-label">Bentuk Integrasi <span class="text-danger">*</span></label>
                        <select name="bentuk_integrasi" class="form-select" required>
                            @foreach($bentukLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun Integrasi <span class="text-danger">*</span></label>
                        <select name="tahun_relatif" class="form-select" required>
                            <option value="TS-2">TS-2</option><option value="TS-1">TS-1</option><option value="TS">TS</option>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Bukti</label><input type="file" name="bukti_file" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editIntegrasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editIntegrasiForm" action="" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Integrasi Penelitian/PkM</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS</label>
                        <select name="dosen_tetap_id" id="edit_integrasi_dosen" class="form-select">
                            <option value="">-</option>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Judul Penelitian/PkM <span class="text-danger">*</span></label><input type="text" name="judul_penelitian_pkm" id="edit_integrasi_judul" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Mata Kuliah Hasil Integrasi</label><input type="text" name="mata_kuliah" id="edit_integrasi_mk" class="form-control"></div>
                    <div class="mb-3">
                        <label class="form-label">Bentuk Integrasi <span class="text-danger">*</span></label>
                        <select name="bentuk_integrasi" id="edit_integrasi_bentuk" class="form-select" required>
                            @foreach($bentukLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun Integrasi <span class="text-danger">*</span></label>
                        <select name="tahun_relatif" id="edit_integrasi_tahun" class="form-select" required>
                            <option value="TS-2">TS-2</option><option value="TS-1">TS-1</option><option value="TS">TS</option>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Ganti Bukti (opsional)</label><input type="file" name="bukti_file" class="form-control"></div>
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
document.getElementById('editIntegrasiModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editIntegrasiForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/integrasi") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_integrasi_dosen').value = btn.getAttribute('data-dosen') || '';
    document.getElementById('edit_integrasi_judul').value = btn.getAttribute('data-judul');
    document.getElementById('edit_integrasi_mk').value = btn.getAttribute('data-mk');
    document.getElementById('edit_integrasi_bentuk').value = btn.getAttribute('data-bentuk');
    document.getElementById('edit_integrasi_tahun').value = btn.getAttribute('data-tahun');
});
</script>
@endpush
