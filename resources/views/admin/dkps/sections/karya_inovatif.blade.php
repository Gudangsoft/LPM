@php
    $kategoriLabel = ['paten' => 'Paten/Paten Sederhana', 'buku_isbn' => 'Buku ber-ISBN/Book Chapter', 'karya_seni' => 'Karya Seni', 'publikasi_jurnal' => 'Publikasi Jurnal (Sinta 5+)'];
    $peringkatLabel = ['sinta_1' => 'SINTA 1', 'sinta_2' => 'SINTA 2', 'sinta_3' => 'SINTA 3', 'sinta_4' => 'SINTA 4', 'sinta_5' => 'SINTA 5', 'jurnal_internasional' => 'Jurnal Internasional', 'jurnal_internasional_bereputasi' => 'Jurnal Internasional Bereputasi'];
@endphp

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addKaryaModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Karya
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr>
                <th>Kategori</th>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Judul</th>
                <th>Tahun</th>
                <th>Keterangan</th>
                <th width="90"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($submission->karyaInovatifMahasiswa as $row)
            <tr>
                <td><span class="badge bg-secondary">{{ $kategoriLabel[$row->kategori] }}</span></td>
                <td>{{ $row->nim }}</td>
                <td>{{ $row->nama_mahasiswa }}</td>
                <td>{{ $row->judul }}</td>
                <td>{{ $row->tahun }}</td>
                <td class="small">{{ $row->keterangan }}{{ $row->peringkat_jurnal ? ' / '.$peringkatLabel[$row->peringkat_jurnal] : '' }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editKaryaModal"
                        data-id="{{ $row->id }}" data-kategori="{{ $row->kategori }}" data-nim="{{ $row->nim }}" data-nama="{{ $row->nama_mahasiswa }}"
                        data-judul="{{ $row->judul }}" data-tahun="{{ $row->tahun }}" data-keterangan="{{ $row->keterangan }}"
                        data-peringkat="{{ $row->peringkat_jurnal }}" data-tautan="{{ $row->tautan }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.karya-inovatif.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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

<div class="modal fade" id="addKaryaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.karya-inovatif.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Karya Inovatif Mahasiswa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required>
                            <option value="paten">Paten/Paten Sederhana</option>
                            <option value="buku_isbn">Buku ber-ISBN/Book Chapter</option>
                            <option value="karya_seni">Karya Seni</option>
                            <option value="publikasi_jurnal">Publikasi Jurnal (min. SINTA 5)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">NIM</label>
                            <input type="text" name="nim" class="form-control">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Nama Mahasiswa <span class="text-danger">*</span></label>
                            <input type="text" name="nama_mahasiswa" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" min="2000" max="2100">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Keterangan (No. Paten / ISBN / Bukti)</label>
                            <input type="text" name="keterangan" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Peringkat Jurnal <span class="small text-muted">(khusus kategori Publikasi Jurnal)</span></label>
                        <select name="peringkat_jurnal" class="form-select">
                            <option value="">-</option>
                            <option value="sinta_1">SINTA 1</option>
                            <option value="sinta_2">SINTA 2</option>
                            <option value="sinta_3">SINTA 3</option>
                            <option value="sinta_4">SINTA 4</option>
                            <option value="sinta_5">SINTA 5</option>
                            <option value="jurnal_internasional">Jurnal Internasional</option>
                            <option value="jurnal_internasional_bereputasi">Jurnal Internasional Bereputasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tautan Artikel</label>
                        <input type="text" name="tautan" class="form-control">
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

<div class="modal fade" id="editKaryaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editKaryaForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Karya Inovatif Mahasiswa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" id="edit_karya_kategori" class="form-select" required>
                            <option value="paten">Paten/Paten Sederhana</option>
                            <option value="buku_isbn">Buku ber-ISBN/Book Chapter</option>
                            <option value="karya_seni">Karya Seni</option>
                            <option value="publikasi_jurnal">Publikasi Jurnal (min. SINTA 5)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">NIM</label>
                            <input type="text" name="nim" id="edit_karya_nim" class="form-control">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Nama Mahasiswa <span class="text-danger">*</span></label>
                            <input type="text" name="nama_mahasiswa" id="edit_karya_nama" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="edit_karya_judul" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" id="edit_karya_tahun" class="form-control" min="2000" max="2100">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Keterangan (No. Paten / ISBN / Bukti)</label>
                            <input type="text" name="keterangan" id="edit_karya_keterangan" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Peringkat Jurnal <span class="small text-muted">(khusus kategori Publikasi Jurnal)</span></label>
                        <select name="peringkat_jurnal" id="edit_karya_peringkat" class="form-select">
                            <option value="">-</option>
                            <option value="sinta_1">SINTA 1</option>
                            <option value="sinta_2">SINTA 2</option>
                            <option value="sinta_3">SINTA 3</option>
                            <option value="sinta_4">SINTA 4</option>
                            <option value="sinta_5">SINTA 5</option>
                            <option value="jurnal_internasional">Jurnal Internasional</option>
                            <option value="jurnal_internasional_bereputasi">Jurnal Internasional Bereputasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tautan Artikel</label>
                        <input type="text" name="tautan" id="edit_karya_tautan" class="form-control">
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
document.getElementById('editKaryaModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editKaryaForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/karya-inovatif") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_karya_kategori').value = btn.getAttribute('data-kategori');
    document.getElementById('edit_karya_nim').value = btn.getAttribute('data-nim');
    document.getElementById('edit_karya_nama').value = btn.getAttribute('data-nama');
    document.getElementById('edit_karya_judul').value = btn.getAttribute('data-judul');
    document.getElementById('edit_karya_tahun').value = btn.getAttribute('data-tahun');
    document.getElementById('edit_karya_keterangan').value = btn.getAttribute('data-keterangan');
    document.getElementById('edit_karya_peringkat').value = btn.getAttribute('data-peringkat') || '';
    document.getElementById('edit_karya_tautan').value = btn.getAttribute('data-tautan');
});
</script>
@endpush
