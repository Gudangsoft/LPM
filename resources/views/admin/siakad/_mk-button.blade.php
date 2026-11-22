{{--
    Tombol "Tarik Mata Kuliah dari SIAKAD" + modal pilih prodi.
    Parameter: $prodis (prodi yang boleh diakses user), $can (boleh mengelola halaman ini).
--}}
@if($can ?? false)
@php
    $mkUser = auth()->user();
    $mkCount = \Illuminate\Support\Facades\Schema::hasTable('mata_kuliah') ? \App\Models\MataKuliah::count() : 0;
@endphp
<button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#siakadMkModal"
        title="Terakhir: {{ \Illuminate\Support\Facades\Cache::get('siakad:last_sync:matakuliah', 'belum pernah') }}">
    <i class="bi bi-cloud-download me-1"></i>Tarik Mata Kuliah dari SIAKAD
</button>

@push('scripts')
<div class="modal fade" id="siakadMkModal" tabindex="-1" aria-labelledby="siakadMkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.siakad.sync.mata-kuliah') }}" method="POST" class="modal-content"
              onsubmit="const b = this.querySelector('button[type=submit]'); b.disabled = true; b.innerHTML = '<span class=&quot;spinner-border spinner-border-sm me-1&quot;></span>Menarik data...';">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="siakadMkModalLabel">Tarik Mata Kuliah dari SIAKAD</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <ul class="small ps-3 mb-3">
                    <li>Daftar mata kuliah prodi diambil dari kurikulum SIAKAD (kode, nama, semester, SKS).</li>
                    <li>Setelah ditarik, kolom <strong>Mata Kuliah</strong> di form Capaian Pembelajaran, Evaluasi Pembelajaran, dan Review RPS
                        menampilkan pilihan sesuai prodi yang dipilih.</li>
                    <li>Data yang sudah Anda isi di halaman ini tidak diubah.</li>
                </ul>
                <label for="siakadMkProdi" class="form-label fw-semibold">Program Studi</label>
                <select name="prodi_id" id="siakadMkProdi" class="form-select" @if(! $mkUser->isAdmin()) required @endif>
                    @if($mkUser->isAdmin())
                    <option value="">Semua prodi yang terhubung SIAKAD</option>
                    @else
                    <option value="">-- Pilih Prodi --</option>
                    @endif
                    @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->jenjang }} {{ $p->nama }}</option>
                    @endforeach
                </select>
                <div class="form-text">Mata kuliah tersimpan saat ini: {{ $mkCount }}.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark"><i class="bi bi-cloud-download me-1"></i>Tarik Sekarang</button>
            </div>
        </form>
    </div>
</div>
@endpush
@endif
