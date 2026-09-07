@extends('layouts.admin')

@php $routePrefix = $routePrefix ?? 'admin.menu-web'; @endphp

@section('title', __('admin.edit_menu'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.edit_menu') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route($routePrefix.'.index') }}">{{ __('admin.menu_management') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.edit') }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route($routePrefix.'.update', $menu) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $menu->nama) }}" required>
                                    @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.type') }} <span class="text-danger">*</span></label>
                                    <select name="tipe" class="form-select @error('tipe') is-invalid @enderror" id="menuType" required>
                                        <option value="link" {{ old('tipe', $menu->tipe) == 'link' ? 'selected' : '' }}>Link</option>
                                        <option value="section" {{ old('tipe', $menu->tipe) == 'section' ? 'selected' : '' }}>Section</option>
                                        <option value="divider" {{ old('tipe', $menu->tipe) == 'divider' ? 'selected' : '' }}>Divider</option>
                                    </select>
                                    @error('tipe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="linkFields" style="{{ $menu->tipe != 'link' ? 'display:none' : '' }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.icon') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="iconPreview"><i class="bi {{ $menu->icon ?? 'bi-circle' }}"></i></span>
                                            <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $menu->icon) }}" placeholder="bi-newspaper" id="iconInput">
                                        </div>
                                        <small class="text-muted">Bootstrap Icons class (e.g., bi-newspaper, bi-gear)</small>
                                        @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.order') }}</label>
                                        <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror" value="{{ old('urutan', $menu->urutan) }}" min="0">
                                        @error('urutan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.route') }}</label>
                                        <input type="text" name="route" class="form-control @error('route') is-invalid @enderror" value="{{ old('route', $menu->route) }}" placeholder="admin.berita.index">
                                        <small class="text-muted">Laravel named route (e.g., admin.berita.index)</small>
                                        @error('route')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.route_pattern') }}</label>
                                        <input type="text" name="route_pattern" class="form-control @error('route_pattern') is-invalid @enderror" value="{{ old('route_pattern', $menu->route_pattern) }}" placeholder="admin.berita.*">
                                        <small class="text-muted">Pattern for active state (e.g., admin.berita.*)</small>
                                        @error('route_pattern')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.url') }}</label>
                                <input type="text" name="url" class="form-control @error('url') is-invalid @enderror" value="{{ old('url', $menu->url) }}" placeholder="https://example.com">
                                <small class="text-muted">Custom URL (used if route is empty)</small>
                                @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.parent') }}</label>
                                <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                    <option value="">-- {{ __('admin.no_parent') }} --</option>
                                    @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $menu->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->nama }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>
                            <h6 class="mb-3">{{ __('admin.badge_settings') }}</h6>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.badge_model') }}</label>
                                        <input type="text" name="badge_model" class="form-control @error('badge_model') is-invalid @enderror" value="{{ old('badge_model', $menu->badge_model) }}" placeholder="\App\Models\Kontak">
                                        @error('badge_model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.badge_method') }}</label>
                                        <input type="text" name="badge_method" class="form-control @error('badge_method') is-invalid @enderror" value="{{ old('badge_method', $menu->badge_method) }}" placeholder="unread">
                                        @error('badge_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.badge_class') }}</label>
                                        <select name="badge_class" class="form-select">
                                            <option value="bg-danger" {{ old('badge_class', $menu->badge_class) == 'bg-danger' ? 'selected' : '' }}>Danger (Red)</option>
                                            <option value="bg-warning" {{ old('badge_class', $menu->badge_class) == 'bg-warning' ? 'selected' : '' }}>Warning (Yellow)</option>
                                            <option value="bg-info" {{ old('badge_class', $menu->badge_class) == 'bg-info' ? 'selected' : '' }}>Info (Blue)</option>
                                            <option value="bg-success" {{ old('badge_class', $menu->badge_class) == 'bg-success' ? 'selected' : '' }}>Success (Green)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ old('is_active', $menu->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">{{ __('admin.active') }}</label>
                            </div>
                            <div class="form-check mt-1" id="newTabField">
                                <input type="checkbox" name="buka_tab" class="form-check-input" id="bukaTab" value="1" {{ old('buka_tab', $menu->buka_tab) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bukaTab">{{ __('admin.open_new_tab') }}</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('admin.update') }}
                            </button>
                            <a href="{{ route($routePrefix.'.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i>{{ __('admin.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">{{ __('admin.common_icons') }}</div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @php
                        $icons = [
                            'bi-speedometer2', 'bi-newspaper', 'bi-folder', 'bi-megaphone',
                            'bi-calendar-event', 'bi-images', 'bi-file-earmark-pdf', 'bi-card-image',
                            'bi-file-text', 'bi-diagram-3', 'bi-envelope', 'bi-people',
                            'bi-gear', 'bi-house', 'bi-star', 'bi-bell', 'bi-chat',
                            'bi-graph-up', 'bi-shield', 'bi-lock', 'bi-key', 'bi-database'
                        ];
                        @endphp
                        @foreach($icons as $icon)
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="{{ $icon }}">
                            <i class="bi {{ $icon }}"></i>
                        </button>
                        @endforeach
                    </div>
                    <small class="text-muted mt-2 d-block">Click icon to use</small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuType = document.getElementById('menuType');
        const linkFields = document.getElementById('linkFields');
        const iconInput = document.getElementById('iconInput');
        const iconPreview = document.getElementById('iconPreview');

        // Toggle fields based on type
        menuType.addEventListener('change', function() {
            if (this.value === 'link') {
                linkFields.style.display = 'block';
            } else {
                linkFields.style.display = 'none';
            }
        });

        // Icon preview
        iconInput.addEventListener('input', function() {
            iconPreview.innerHTML = '<i class="bi ' + this.value + '"></i>';
        });

        // Icon buttons
        document.querySelectorAll('.icon-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const icon = this.getAttribute('data-icon');
                iconInput.value = icon;
                iconPreview.innerHTML = '<i class="bi ' + icon + '"></i>';
            });
        });
    });
</script>
@endpush
