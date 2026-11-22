{{--
    Saran Mata Kuliah dari SIAKAD untuk input name="mata_kuliah" (list="mkList").
    Pilihan disaring sesuai select name="prodi_id" di form yang sama. Bila form punya
    input kode_mk / sks yang masih kosong, ikut diisi saat mata kuliah dipilih.
--}}
@php
    $mkOptions = \Illuminate\Support\Facades\Schema::hasTable('mata_kuliah')
        ? \App\Models\MataKuliah::orderBy('semester')->orderBy('nama')->get(['prodi_id', 'kode_mk', 'nama', 'semester', 'sks'])
        : collect();
@endphp
<datalist id="mkList"></datalist>
@if($mkOptions->isNotEmpty())
<div class="form-text">Ketik untuk memilih dari mata kuliah SIAKAD (sesuai prodi), atau isi manual.</div>
@push('scripts')
<script>
(() => {
    const data = @json($mkOptions);
    const input = document.querySelector('input[name="mata_kuliah"]');
    if (!input) return;
    const form = input.form;
    const prodi = form?.querySelector('select[name="prodi_id"]');
    const list = document.getElementById('mkList');
    input.setAttribute('list', 'mkList');
    input.setAttribute('autocomplete', 'off');

    const pilihan = () => data.filter(m => !prodi || !prodi.value || String(m.prodi_id) === prodi.value);
    const render = () => {
        list.innerHTML = '';
        pilihan().forEach(m => {
            const o = document.createElement('option');
            o.value = m.nama;
            o.textContent = [m.kode_mk, m.semester ? 'Smt ' + m.semester : null, m.sks ? m.sks + ' SKS' : null].filter(Boolean).join(' · ');
            list.appendChild(o);
        });
    };
    prodi?.addEventListener('change', render);
    render();

    input.addEventListener('change', () => {
        const m = pilihan().find(x => x.nama === input.value);
        if (!m) return;
        const kode = form?.querySelector('input[name="kode_mk"]');
        const sks = form?.querySelector('input[name="sks"]');
        if (kode && !kode.value && m.kode_mk) kode.value = m.kode_mk;
        if (sks && !sks.value && m.sks) sks.value = Math.round(m.sks);
    });
})();
</script>
@endpush
@endif
