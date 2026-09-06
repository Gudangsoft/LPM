@php
    $jenisLabel = ['pustakawan' => 'Pustakawan', 'laboran' => 'Laboran/Teknisi/Analis/Operator/Programmer/Pranata Komputer', 'administrasi' => 'Administrasi', 'lainnya' => 'Lainnya'];
@endphp

<h6 class="text-muted">Rekapitulasi Jumlah Tenaga Kependidikan</h6>
<form action="{{ route('admin.dkps.tendik-summary.update', $submission) }}" method="POST" class="mb-4">
    @csrf
    @method('PUT')
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Jenis</th>
                    <th width="70">S3</th>
                    <th width="70">S2</th>
                    <th width="70">S1</th>
                    <th width="70">D4</th>
                    <th width="70">D3</th>
                    <th width="80">SMA/SMK</th>
                    <th>Unit Kerja</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submission->tenagaKependidikanSummary as $i => $row)
                <tr>
                    <td class="small">
                        {{ $jenisLabel[$row->jenis] }}
                        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    @foreach(['jumlah_s3','jumlah_s2','jumlah_s1','jumlah_d4','jumlah_d3','jumlah_sma_smk'] as $field)
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][{{ $field }}]" value="{{ $row->$field }}"></td>
                    @endforeach
                    <td><input type="text" class="form-control form-control-sm" name="rows[{{ $i }}][unit_kerja]" value="{{ $row->unit_kerja }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan Rekapitulasi</button>
</form>

<hr>

<h6 class="text-muted d-flex justify-content-between align-items-center">
    Daftar Nama Tenaga Kependidikan
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTendikModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah
    </button>
</h6>
<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light"><tr><th>Nama</th><th>Jenis</th><th width="90"></th></tr></thead>
        <tbody>
            @forelse($submission->tenagaKependidikan as $row)
            <tr>
                <td>{{ $row->nama }}</td>
                <td>{{ $jenisLabel[$row->jenis] }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editTendikModal" data-id="{{ $row->id }}" data-nama="{{ $row->nama }}" data-jenis="{{ $row->jenis }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.tendik.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="addTendikModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.dkps.tendik.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Tenaga Kependidikan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" required></div>
                    <div class="mb-3">
                        <label class="form-label">Jenis <span class="text-danger">*</span></label>
                        <select name="jenis" class="form-select" required>
                            @foreach($jenisLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                        </select>
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

<div class="modal fade" id="editTendikModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editTendikForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Tenaga Kependidikan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="nama" id="edit_tendik_nama" class="form-control" required></div>
                    <div class="mb-3">
                        <label class="form-label">Jenis <span class="text-danger">*</span></label>
                        <select name="jenis" id="edit_tendik_jenis" class="form-select" required>
                            @foreach($jenisLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                        </select>
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
document.getElementById('editTendikModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editTendikForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/tendik") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_tendik_nama').value = btn.getAttribute('data-nama');
    document.getElementById('edit_tendik_jenis').value = btn.getAttribute('data-jenis');
});
</script>
@endpush
