<form action="{{ route($route) }}" method="POST" class="mt-auto"
      onsubmit="const b = this.querySelector('button[type=submit]'); b.disabled = true; b.innerHTML = '<span class=&quot;spinner-border spinner-border-sm me-1&quot;></span>Menarik data...';">
    @csrf
    <label for="{{ $idPrefix }}Dkps" class="form-label small fw-semibold mb-1">DKPS tujuan</label>
    @if($dkpsList->isEmpty())
        <div class="small text-danger mb-2">
            Belum ada DKPS. Buat dulu di menu <a href="{{ route('admin.dkps.create') }}">DKPS</a>.
        </div>
    @else
        <select name="dkps_id" id="{{ $idPrefix }}Dkps" class="form-select form-select-sm mb-3" required>
            <option value="">-- Pilih DKPS (prodi &amp; tahun TS) --</option>
            @foreach($dkpsList as $d)
            <option value="{{ $d->id }}">{{ $d->prodi?->jenjang }} {{ $d->prodi?->nama }} &middot; TS {{ $d->tahun_ts_awal }}/{{ $d->tahun_ts_akhir }}</option>
            @endforeach
        </select>
    @endif
    <button type="submit" class="btn btn-dark" @disabled($dkpsList->isEmpty())><i class="bi bi-cloud-download me-1"></i>{{ $label }}</button>
</form>
