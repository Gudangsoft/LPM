@if(session('import_errors'))
<div class="alert alert-warning">
    <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-1"></i>{{ session('import_errors_title', 'Detail dari SIAKAD:') }}</div>
    <ul class="mb-0 small" style="max-height: 220px; overflow-y: auto;">
        @foreach(session('import_errors') as $err)<li>{{ $err }}</li>@endforeach
    </ul>
</div>
@endif
