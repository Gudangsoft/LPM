@php
    $peranLabel = ['penulis_pertama' => 'Penulis Pertama', 'corresponding_author' => 'Corresponding Author'];
    $jenisLabel = ['nasional' => 'Jurnal Nasional', 'internasional' => 'Jurnal Internasional'];
    $terindeksLabel = ['scopus_q1' => 'Scopus Q1', 'scopus_q2' => 'Scopus Q2', 'scopus_q3' => 'Scopus Q3', 'scopus_q4' => 'Scopus Q4', 'wos' => 'WoS', 'sinta_1' => 'SINTA 1', 'sinta_2' => 'SINTA 2', 'sinta_3' => 'SINTA 3', 'sinta_4' => 'SINTA 4'];
@endphp

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPublikasiModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Publikasi
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light"><tr><th>Nama DTPS</th><th>Judul Artikel</th><th>Penulis</th><th>Peran</th><th>Jenis</th><th>Terindeks</th><th>Tanggal Terbit</th><th width="90"></th></tr></thead>
        <tbody>
            @forelse($submission->publikasiDtpsDetail as $row)
            <tr>
                <td>{{ $row->dosenTetap->nama ?? '-' }}</td>
                <td class="small">{{ Str::limit($row->judul_artikel, 60) }}</td>
                <td>{{ $row->nama_penulis }}</td>
                <td>{{ $peranLabel[$row->penulis_peran] }}</td>
                <td>{{ $jenisLabel[$row->jenis_jurnal] }}</td>
                <td>{{ $terindeksLabel[$row->terindeks] }}</td>
                <td>{{ optional($row->tanggal_terbit)->format('d/m/Y') }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPublikasiModal"
                        data-id="{{ $row->id }}" data-dosen="{{ $row->dosen_tetap_id }}" data-judul="{{ $row->judul_artikel }}"
                        data-penulis="{{ $row->nama_penulis }}" data-peran="{{ $row->penulis_peran }}" data-jenis="{{ $row->jenis_jurnal }}"
                        data-terindeks="{{ $row->terindeks }}" data-tanggal="{{ optional($row->tanggal_terbit)->format('Y-m-d') }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.dkps.publikasi-detail.destroy', [$submission, $row]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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

<div class="modal fade" id="addPublikasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.dkps.publikasi-detail.store', $submission) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Publikasi DTPS</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS</label>
                        <select name="dosen_tetap_id" class="form-select">
                            <option value="">-</option>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Judul Artikel (Jurnal, Volume, Tahun, Nomor, Halaman) <span class="text-danger">*</span></label><textarea name="judul_artikel" class="form-control" rows="2" required></textarea></div>
                    <div class="mb-3"><label class="form-label">Nama Penulis</label><input type="text" name="nama_penulis" class="form-control"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Peran Penulis <span class="text-danger">*</span></label>
                            <select name="penulis_peran" class="form-select" required>
                                @foreach($peranLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Jurnal <span class="text-danger">*</span></label>
                            <select name="jenis_jurnal" class="form-select" required>
                                @foreach($jenisLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Terindeks <span class="text-danger">*</span></label>
                            <select name="terindeks" class="form-select" required>
                                @foreach($terindeksLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Terbit</label><input type="date" name="tanggal_terbit" class="form-control"></div>
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

<div class="modal fade" id="editPublikasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editPublikasiForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Publikasi DTPS</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama DTPS</label>
                        <select name="dosen_tetap_id" id="edit_publikasi_dosen" class="form-select">
                            <option value="">-</option>
                            @foreach($submission->dosenTetap as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Judul Artikel (Jurnal, Volume, Tahun, Nomor, Halaman) <span class="text-danger">*</span></label><textarea name="judul_artikel" id="edit_publikasi_judul" class="form-control" rows="2" required></textarea></div>
                    <div class="mb-3"><label class="form-label">Nama Penulis</label><input type="text" name="nama_penulis" id="edit_publikasi_penulis" class="form-control"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Peran Penulis <span class="text-danger">*</span></label>
                            <select name="penulis_peran" id="edit_publikasi_peran" class="form-select" required>
                                @foreach($peranLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Jurnal <span class="text-danger">*</span></label>
                            <select name="jenis_jurnal" id="edit_publikasi_jenis" class="form-select" required>
                                @foreach($jenisLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Terindeks <span class="text-danger">*</span></label>
                            <select name="terindeks" id="edit_publikasi_terindeks" class="form-select" required>
                                @foreach($terindeksLabel as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Terbit</label><input type="date" name="tanggal_terbit" id="edit_publikasi_tanggal" class="form-control"></div>
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
document.getElementById('editPublikasiModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editPublikasiForm');
    form.action = '{{ url("admin/dkps/{$submission->id}/publikasi-detail") }}/' + btn.getAttribute('data-id');
    document.getElementById('edit_publikasi_dosen').value = btn.getAttribute('data-dosen') || '';
    document.getElementById('edit_publikasi_judul').value = btn.getAttribute('data-judul');
    document.getElementById('edit_publikasi_penulis').value = btn.getAttribute('data-penulis');
    document.getElementById('edit_publikasi_peran').value = btn.getAttribute('data-peran');
    document.getElementById('edit_publikasi_jenis').value = btn.getAttribute('data-jenis');
    document.getElementById('edit_publikasi_terindeks').value = btn.getAttribute('data-terindeks');
    document.getElementById('edit_publikasi_tanggal').value = btn.getAttribute('data-tanggal');
});
</script>
@endpush
