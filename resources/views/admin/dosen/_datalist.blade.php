{{-- Saran nama dosen dari menu Data Dosen untuk input teks dengan list="dosenList". --}}
@php
    $dosenOptions = auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('dosen')
        ? \App\Models\Dosen::with('prodi:id,nama')->visibleTo(auth()->user())->where('is_active', true)->orderBy('nama')->get(['id', 'nama', 'prodi_id'])
        : collect();
@endphp
<datalist id="dosenList">
    @foreach($dosenOptions as $opt)
    <option value="{{ $opt->nama }}">{{ optional($opt->prodi)->nama }}</option>
    @endforeach
</datalist>
@if($dosenOptions->isNotEmpty())
<div class="form-text">Ketik untuk memilih dari Data Dosen, atau isi manual.</div>
@endif
