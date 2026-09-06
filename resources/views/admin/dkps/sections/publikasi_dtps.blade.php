@php
    $mediaLabel = [
        'jurnal_nasional_tidak_terakreditasi' => 'Jurnal nasional tidak terakreditasi',
        'jurnal_nasional_terakreditasi' => 'Jurnal nasional terakreditasi',
        'jurnal_internasional_karya_monumental_nasional' => 'Jurnal Internasional/karya monumental tingkat nasional',
        'jurnal_internasional_bereputasi_karya_monumental_internasional' => 'Jurnal internasional bereputasi/karya monumental tingkat internasional',
        'seminar_wilayah_lokal_pt' => 'Seminar wilayah/lokal/perguruan tinggi',
        'seminar_nasional' => 'Seminar nasional',
        'seminar_internasional' => 'Seminar internasional',
        'media_massa_wilayah' => 'Tulisan di media massa wilayah',
        'media_massa_nasional' => 'Tulisan di media massa nasional',
        'media_massa_internasional' => 'Tulisan di media massa internasional',
        'buku_isbn_book_chapter' => 'Buku Ber-ISBN/Book Chapter',
        'paten_paten_sederhana' => 'Paten/Paten Sederhana',
    ];
@endphp

<form action="{{ route('admin.dkps.publikasi-dtps.update', $submission) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light"><tr><th>Media Publikasi</th><th width="110">TS-2</th><th width="110">TS-1</th><th width="110">TS</th></tr></thead>
            <tbody>
                @foreach($submission->publikasiDtps as $i => $row)
                <tr>
                    <td class="small">
                        {{ $mediaLabel[$row->media_publikasi] }}
                        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id }}">
                    </td>
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][jumlah_ts2]" value="{{ $row->jumlah_ts2 }}"></td>
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][jumlah_ts1]" value="{{ $row->jumlah_ts1 }}"></td>
                    <td><input type="number" min="0" class="form-control form-control-sm" name="rows[{{ $i }}][jumlah_ts]" value="{{ $row->jumlah_ts }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
</form>
