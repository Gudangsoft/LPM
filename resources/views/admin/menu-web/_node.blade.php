@php
    $hasKids = $node->relationLoaded('childrenRecursive') && $node->childrenRecursive->isNotEmpty();
    $routePrefix = $routePrefix ?? 'admin.menu-web';
@endphp
<li class="menu-node" data-id="{{ $node->id }}" data-type="{{ $node->tipe }}" data-depth="{{ $depth }}">
    <div class="menu-node__row menu-node__row--{{ $node->tipe }} {{ $node->is_active ? '' : 'is-inactive' }}">
        <span class="menu-node__handle" title="{{ __('admin.drag_to_reorder') }}"><i class="bi bi-grip-vertical"></i></span>

        @if($hasKids)
        <button type="button" class="menu-node__toggle" aria-expanded="true"><i class="bi bi-caret-down-fill"></i></button>
        @else
        <span class="menu-node__toggle menu-node__toggle--empty"></span>
        @endif

        <span class="menu-node__icon">
            @if($node->tipe === 'divider')
                <i class="bi bi-dash-lg"></i>
            @elseif($node->tipe === 'section')
                <i class="bi bi-type-h3"></i>
            @elseif($node->icon)
                <i class="bi {{ $node->icon }}"></i>
            @else
                <i class="bi bi-record-circle"></i>
            @endif
        </span>

        <span class="menu-node__name js-node-name">{{ $node->nama }}</span>

        <span class="menu-node__meta">
            <span class="js-node-target">
                @if($node->tipe !== 'link')
                    <span class="badge bg-secondary text-uppercase">{{ $node->tipe }}</span>
                @elseif($node->route)
                    <code class="small">{{ $node->route }}</code>
                @elseif($node->url)
                    <span class="text-muted small">{{ \Illuminate\Support\Str::limit($node->url, 40) }}</span>
                @endif
            </span>
            @if($node->buka_tab)
                <span class="badge bg-light text-dark border js-node-newtab" title="{{ __('admin.open_new_tab') }}"><i class="bi bi-box-arrow-up-right"></i></span>
            @endif
            @unless($node->is_active)
                <span class="badge bg-warning text-dark js-node-inactive">{{ __('admin.inactive') }}</span>
            @endunless
        </span>

        <span class="menu-node__actions">
            <button type="button" class="btn btn-sm btn-light js-quick-edit" title="{{ __('admin.quick_edit') }}"><i class="bi bi-pencil"></i></button>
            <a href="{{ route($routePrefix.'.edit', $node) }}" class="btn btn-sm btn-light" title="{{ __('admin.full_edit') }}"><i class="bi bi-box-arrow-up-right"></i></a>
            <form action="{{ route($routePrefix.'.destroy', $node) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-light text-danger" title="{{ __('admin.delete') }}"><i class="bi bi-trash"></i></button>
            </form>
        </span>
    </div>

    <div class="menu-node__quick" hidden data-quick-url="{{ route($routePrefix.'.quick', $node) }}">
        <div class="row g-2">
            <div class="col-sm-5">
                <label class="form-label small mb-1">{{ __('admin.name') }}</label>
                <input type="text" class="form-control form-control-sm js-qe-nama" value="{{ $node->nama }}">
            </div>
            <div class="col-sm-3">
                <label class="form-label small mb-1">{{ __('admin.icon') }}</label>
                <input type="text" class="form-control form-control-sm js-qe-icon" value="{{ $node->icon }}" placeholder="bi-house">
            </div>
            <div class="col-sm-2 d-flex align-items-end">
                <div class="form-check form-switch mb-1">
                    <input type="checkbox" class="form-check-input js-qe-active" {{ $node->is_active ? 'checked' : '' }}>
                    <label class="form-check-label small">{{ __('admin.active') }}</label>
                </div>
            </div>
            <div class="col-sm-2 d-flex align-items-end">
                <div class="form-check form-switch mb-1">
                    <input type="checkbox" class="form-check-input js-qe-newtab" {{ $node->buka_tab ? 'checked' : '' }}>
                    <label class="form-check-label small">{{ __('admin.new_tab_short') }}</label>
                </div>
            </div>
        </div>
        @if($node->tipe === 'link')
        <div class="row g-2 mt-1">
            <div class="col-12">
                <label class="form-label small mb-1">{{ __('admin.target') }} / Link</label>
                <input type="text" class="form-control form-control-sm js-qe-target"
                       value="{{ $node->route ?: $node->url }}"
                       placeholder="admin.berita.index  •  /halaman/anu  •  https://situs.lain">
                <small class="text-muted">{{ __('admin.target_hint') }}</small>
            </div>
        </div>
        @endif
        <div class="mt-2 d-flex gap-2">
            <button type="button" class="btn btn-sm btn-primary js-qe-save">{{ __('admin.save') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary js-qe-cancel">{{ __('admin.cancel') }}</button>
        </div>
    </div>

    @if($depth < \App\Models\Menu::MAX_DEPTH && $node->tipe === 'link')
    <ul class="menu-tree" data-depth="{{ $depth + 1 }}">
        @if($hasKids)
            @foreach($node->childrenRecursive as $child)
                @include('admin.menu-web._node', ['node' => $child, 'depth' => $depth + 1, 'routePrefix' => $routePrefix])
            @endforeach
        @endif
    </ul>
    @endif
</li>
