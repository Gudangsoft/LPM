@php
    $bidangLabel = ['pendidikan' => 'Pendidikan', 'penelitian' => 'Penelitian', 'pkm' => 'PkM'];
    $tingkatLabel = ['wilayah_lokal' => 'Wilayah/Lokal', 'nasional' => 'Nasional', 'internasional' => 'Internasional'];
@endphp

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addKerjasamaModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Kerjasama
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr>
                <th>Bidang</th>
                <th>Lembaga Mitra</th>
                <th>Tingkat</th>
                <th>Judul Kegiatan</th>
                <th>Periode</th>
                <th>Bukti</th>
                <th width="90"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($submission->kerjasama as $row)
            <tr>
                <td><span class="badge bg-secondary">{{ $bidangLabel[$row->bidang] }}</span></td>
                <td>{{ $row->lembaga_mitra }}</td>
                <td>{{ $tingkatLabel[$row->tingkat] }}</td>
                <td>{{ $row->judul_kegiatan }}</td>
                <td class="small text-nowrap">
                    {{ optional($row->tanggal_awal)->format('d/m/Y') }} -
                    {{ optional($row->tanggal_akhir)->format('d/m/Y') }}
                </td>
                <td>
                    @if($row->bukti_file)
                    <a href="{{ Storage::url($row->bukti_file) }}" target="_blank"><i class="bi bi-file-earmark-check"></i></a>
                    @else
                    -
                    @endif
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editKerjasamaModal"
                        data-id="{{ $row->id }}" data-bidang="{{ $row->bidang }}" data-lembaga="{{ $row->lembaga_mitra }}"
                        data-tingkat="{{ $row->tingkat }}" data-judul="{{ $row->judul_kegiatan }}" data-manfaat="{{ $row->manfaat }}"
                        data-awal="{{ optional($row->tanggal_awal)->format('Y-m-d') }}" data-akhir="{{ optional($row->tanggal_akhir)->format('Y-m-d') }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.kerjasama.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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

{{-- Add Modal --}}
<div class="modal fade" id="addKerjasamaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.kerjasama.store', $submission) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Kerjasama</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bidang <span class="text-danger">*</span></label>
                            <select name="bidang" class="form-select" required>
                                <option value="pendidikan">Pendidikan</option>
                                <option value="penelitian">Penelitian</option>
                                <option value="pkm">PkM</option>
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Lembaga Mitra <span class="text-danger">*</span></label>
                            <input type="text" name="lembaga_mitra" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                        <select name="tingkat" class="form-select" required>
                            <option value="wilayah_lokal">Wilayah/Lokal</option>
                            <option value="nasional">Nasional</option>
                            <option value="internasional">Internasional</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul Kegiatan Kerjasama <span class="text-danger">*</span></label>
                        <input type="text" name="judul_kegiatan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Manfaat bagi PS</label>
                        <textarea name="manfaat" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" name="tanggal_awal" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bukti Kerjasama (PDF/Gambar)</label>
                        <input type="file" name="bukti_file" class="form-control">
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

{{-- Edit Modal (shared, filled via JS) --}}
<div class="modal fade" id="editKerjasamaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editKerjasamaForm" action="" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Kerjasama</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bidang <span class="text-danger">*</span></label>
                            <select name="bidang" id="edit_kerjasama_bidang" class="form-select" required>
                                <option value="pendidikan">Pendidikan</option>
                                <option value="penelitian">Penelitian</option>
                                <option value="pkm">PkM</option>
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Lembaga Mitra <span class="text-danger">*</span></label>
                            <input type="text" name="lembaga_mitra" id="edit_kerjasama_lembaga" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                        <select name="tingkat" id="edit_kerjasama_tingkat" class="form-select" required>
                            <option value="wilayah_lokal">Wilayah/Lokal</option>
                            <option value="nasional">Nasional</option>
                            <option value="internasional">Internasional</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul Kegiatan Kerjasama <span class="text-danger">*</span></label>
                        <input type="text" name="judul_kegiatan" id="edit_kerjasama_judul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Manfaat bagi PS</label>
                        <textarea name="manfaat" id="edit_kerjasama_manfaat" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" name="tanggal_awal" id="edit_kerjasama_awal" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" id="edit_kerjasama_akhir" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Bukti Kerjasama (opsional)</label>
                        <input type="file" name="bukti_file" class="form-control">
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
document.getElementById('editKerjasamaModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editKerjasamaForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/kerjasama") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_kerjasama_bidang').value = btn.getAttribute('data-bidang');
    document.getElementById('edit_kerjasama_lembaga').value = btn.getAttribute('data-lembaga');
    document.getElementById('edit_kerjasama_tingkat').value = btn.getAttribute('data-tingkat');
    document.getElementById('edit_kerjasama_judul').value = btn.getAttribute('data-judul');
    document.getElementById('edit_kerjasama_manfaat').value = btn.getAttribute('data-manfaat');
    document.getElementById('edit_kerjasama_awal').value = btn.getAttribute('data-awal');
    document.getElementById('edit_kerjasama_akhir').value = btn.getAttribute('data-akhir');
});
</script>
@endpush
