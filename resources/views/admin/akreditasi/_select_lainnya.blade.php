{{--
    Dropdown dengan opsi "+ Tambah ... lain" yang memunculkan isian teks bebas.
    Parameter: $name, $label, $options (array), $value (nilai tersimpan/old), $noun (mis. "lembaga").
    Nilai "__lainnya" + isian "{name}_lain" diolah di AkreditasiController::applyLainnya().
--}}
@php
    $current = old($name) === '__lainnya' ? '__lainnya' : (string) old($name, $value ?? '');
    // Nilai lama yang ejaannya beda dari daftar (mis. "LAM-Teknik") tetap bisa terpilih.
    if ($current !== '' && $current !== '__lainnya' && ! in_array($current, $options, true)) {
        $options[] = $current;
    }
    $isLain = $current === '__lainnya';
@endphp
<label for="{{ $name }}" class="form-label">{{ $label }} <span class="text-danger">*</span></label>
<select class="form-select select-lainnya @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" data-lain-target="{{ $name }}_lain_wrap" required>
    <option value="">Pilih {{ ucfirst($noun) }}</option>
    @foreach($options as $opt)
    <option value="{{ $opt }}" {{ $current === $opt ? 'selected' : '' }}>{{ $opt }}</option>
    @endforeach
    <option value="__lainnya" {{ $isLain ? 'selected' : '' }}>+ Tambah {{ $noun }} lain…</option>
</select>
<div id="{{ $name }}_lain_wrap" class="mt-2 {{ $isLain ? '' : 'd-none' }}">
    <input type="text" class="form-control @error($name.'_lain') is-invalid @enderror" name="{{ $name }}_lain" id="{{ $name }}_lain"
           value="{{ old($name.'_lain') }}" maxlength="{{ $name === 'lembaga' ? 100 : 50 }}" placeholder="Ketik nama {{ $noun }}" {{ $isLain ? 'required' : '' }}>
    <div class="form-text">Setelah disimpan, {{ $noun }} ini otomatis muncul di daftar pilihan berikutnya.</div>
</div>
@error($name)
<div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

@once
@push('scripts')
<script>
document.querySelectorAll('.select-lainnya').forEach(function (sel) {
    const wrap = document.getElementById(sel.dataset.lainTarget);
    const input = wrap.querySelector('input');
    const sync = function () {
        const on = sel.value === '__lainnya';
        wrap.classList.toggle('d-none', !on);
        input.required = on;
        if (on) input.focus();
    };
    sel.addEventListener('change', sync);
});
</script>
@endpush
@endonce
