@extends('layouts.admin')

@php $routePrefix = $routePrefix ?? 'admin.menu'; $pageTitle = $pageTitle ?? __('admin.menu_editor'); @endphp

@section('title', $pageTitle)

@push('styles')
<style>
    .menu-editor-hint { color: #64748b; font-size: .875rem; margin-bottom: 1rem; }

    .menu-tree, .menu-tree ul { list-style: none; margin: 0; padding: 0; }
    #menuTreeRoot { padding: 4px; }
    .menu-tree ul.menu-tree { margin-left: 34px; border-left: 2px dashed #e2e8f0; padding-left: 6px; }
    .menu-tree ul.menu-tree:empty { min-height: 12px; }
    .menu-tree ul.menu-tree.drop-hover { border-left-color: var(--secondary-color); background: rgba(59,130,246,.04); }

    .menu-node { margin: 6px 0; }
    .menu-node__row {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 9px 12px;
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
    }
    .menu-node__row.is-inactive { opacity: .6; }
    .menu-node__row--section { background: #f1f5f9; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; font-size: .82rem; }
    .menu-node__row--divider { background: repeating-linear-gradient(90deg,#f8fafc,#f8fafc 6px,#eef2f7 6px,#eef2f7 12px); }

    .menu-node__handle { cursor: grab; color: #94a3b8; padding: 2px; }
    .menu-node__handle:active { cursor: grabbing; }
    .menu-node__toggle {
        border: 0; background: transparent; color: #64748b; width: 22px; height: 22px;
        display: inline-flex; align-items: center; justify-content: center; cursor: pointer; padding: 0;
        transition: transform .15s ease;
    }
    .menu-node__toggle--empty { width: 22px; display: inline-block; }
    .menu-node.is-collapsed > .menu-node__row .menu-node__toggle { transform: rotate(-90deg); }
    .menu-node.is-collapsed > ul.menu-tree { display: none; }

    .menu-node__icon { color: var(--primary-color); width: 22px; text-align: center; }
    .menu-node__name { font-weight: 500; }
    .menu-node__meta { display: flex; align-items: center; gap: 8px; margin-left: 6px; }
    .menu-node__meta code { background: #f1f5f9; padding: 1px 6px; border-radius: 5px; color: #475569; }
    .menu-node__actions { margin-left: auto; display: flex; gap: 4px; flex-shrink: 0; }
    .menu-node__actions .btn { padding: 3px 8px; }

    .menu-node__quick { background: #f8fafc; border: 1px solid #e2e8f0; border-top: 0; border-radius: 0 0 10px 10px; padding: 12px; margin-top: -6px; }

    .sortable-ghost > .menu-node__row { border-style: dashed; background: #eff6ff; }
    .sortable-drag { opacity: .9; }

    .menu-save-status { font-size: .82rem; color: #64748b; display: inline-flex; align-items: center; gap: 6px; }
    .menu-save-status.is-saving { color: #b45309; }
    .menu-save-status.is-saved { color: #059669; }
    .menu-save-status.is-error { color: #dc2626; }
</style>
@endpush

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">{{ $pageTitle }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">{{ __('admin.menu_management') }}</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMenuModal">
            <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_menu_item') }}
        </button>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>{{ __('admin.menu_list') }}</span>
            <div class="d-flex align-items-center gap-3">
                <span class="menu-save-status" id="menuSaveStatus"></span>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary" id="btnExpandAll">{{ __('admin.expand_all') }}</button>
                    <button type="button" class="btn btn-outline-secondary" id="btnCollapseAll">{{ __('admin.collapse_all') }}</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <p class="menu-editor-hint"><i class="bi bi-info-circle me-1"></i>{{ __('admin.menu_editor_hint') }}</p>

            @if($tree->isEmpty())
                <div class="text-center text-muted py-5">{{ __('admin.no_menu_items') }}</div>
            @endif

            <ul class="menu-tree" id="menuTreeRoot" data-depth="1">
                @foreach($tree as $item)
                    @include('admin.menu._node', ['node' => $item, 'depth' => 1, 'routePrefix' => $routePrefix])
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Add item modal --}}
    <div class="modal fade" id="addMenuModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route($routePrefix.'.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('admin.add_menu_item') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('admin.cancel') }}"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">{{ __('admin.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" required value="{{ old('nama') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('admin.type') }} <span class="text-danger">*</span></label>
                                <select name="tipe" class="form-select" id="addMenuType">
                                    <option value="link">Link</option>
                                    <option value="section">Section</option>
                                    <option value="divider">Divider</option>
                                </select>
                            </div>

                            <div class="col-12 add-link-field">
                                <label class="form-label">{{ __('admin.parent') }}</label>
                                <select name="parent_id" class="form-select">
                                    <option value="">-- {{ __('admin.no_parent') }} --</option>
                                    @foreach($parentOptions as $p)
                                    <option value="{{ $p->id }}">{{ str_repeat('— ', max(0, $p->depth() - 1)) }}{{ $p->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 add-link-field">
                                <label class="form-label">{{ __('admin.icon') }}</label>
                                <input type="text" name="icon" class="form-control" placeholder="bi-house" value="{{ old('icon') }}">
                                <small class="text-muted">Bootstrap Icons (bi-house, bi-gear, …)</small>
                            </div>
                            <div class="col-md-6 add-link-field">
                                <label class="form-label">{{ __('admin.route') }}</label>
                                <input type="text" name="route" class="form-control" placeholder="admin.berita.index" value="{{ old('route') }}">
                            </div>
                            <div class="col-md-6 add-link-field">
                                <label class="form-label">{{ __('admin.route_pattern') }}</label>
                                <input type="text" name="route_pattern" class="form-control" placeholder="admin.berita.*" value="{{ old('route_pattern') }}">
                            </div>
                            <div class="col-md-6 add-link-field">
                                <label class="form-label">{{ __('admin.url') }}</label>
                                <input type="text" name="url" class="form-control" placeholder="https://…" value="{{ old('url') }}">
                            </div>
                            <div class="col-md-6 add-link-field">
                                <label class="form-label">{{ __('admin.role') }} / permission</label>
                                <input type="text" name="permission" class="form-control" placeholder="dashboard.view" value="{{ old('permission') }}">
                            </div>
                        </div>
                        <div class="form-check form-switch mt-3">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="addMenuActive" checked>
                            <label class="form-check-label" for="addMenuActive">{{ __('admin.active') }}</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const MAX_DEPTH = {{ \App\Models\Menu::MAX_DEPTH }};
    const root = document.getElementById('menuTreeRoot');
    if (!root) return;

    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const treeUrl = @json(route($routePrefix.'.tree'));
    const status = document.getElementById('menuSaveStatus');
    const T = {
        saving: @json(__('admin.saving')),
        saved: @json(__('admin.menu_order_saved')),
        error: @json(__('admin.save_failed')),
        maxDepth: @json(__('admin.max_depth_reached')),
    };

    // ---- helpers -------------------------------------------------------
    function subtreeHeight(li) {
        const kids = li.querySelectorAll(':scope > ul.menu-tree > li.menu-node');
        if (!kids.length) return 1;
        let max = 0;
        kids.forEach(k => { max = Math.max(max, subtreeHeight(k)); });
        return 1 + max;
    }
    function listDepth(ul) { return parseInt(ul.dataset.depth || '1', 10); }

    function serialize(ul) {
        return [...ul.children]
            .filter(li => li.classList.contains('menu-node'))
            .map(li => {
                const childUl = li.querySelector(':scope > ul.menu-tree');
                return { id: li.dataset.id, children: childUl ? serialize(childUl) : [] };
            });
    }

    let saveTimer = null;
    function scheduleSave() {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(save, 500);
    }
    function setStatus(cls, text) {
        status.className = 'menu-save-status ' + cls;
        status.innerHTML = (cls === 'is-saving' ? '<span class="spinner-border spinner-border-sm"></span> ' : '') + text;
    }
    function save() {
        setStatus('is-saving', T.saving);
        fetch(treeUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ tree: serialize(root) }),
        })
        .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(() => { setStatus('is-saved', '<i class="bi bi-check-circle"></i> ' + T.saved); })
        .catch(() => { setStatus('is-error', '<i class="bi bi-exclamation-triangle"></i> ' + T.error); });
    }

    // ---- sortable init ----------------------------------------------------
    function initSortable(ul) {
        new Sortable(ul, {
            group: 'menu',
            handle: '.menu-node__handle',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.6,
            draggable: '.menu-node',
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onMove: function (evt) {
                const target = evt.to;                // <ul> being dropped into
                const dragged = evt.dragged;          // <li>
                const targetDepth = listDepth(target);
                // section / divider may only live at the root
                if (dragged.dataset.type !== 'link' && targetDepth !== 1) return false;
                // depth limit
                if (targetDepth - 1 + subtreeHeight(dragged) > MAX_DEPTH) return false;
                return true;
            },
            onEnd: function () { scheduleSave(); },
        });
    }
    initSortable(root);
    root.querySelectorAll('ul.menu-tree').forEach(initSortable);

    // ---- collapse / expand ---------------------------------------------
    root.addEventListener('click', function (e) {
        const toggle = e.target.closest('.menu-node__toggle');
        if (toggle && !toggle.classList.contains('menu-node__toggle--empty')) {
            toggle.closest('.menu-node').classList.toggle('is-collapsed');
        }
    });
    document.getElementById('btnCollapseAll').addEventListener('click', () =>
        root.querySelectorAll('.menu-node').forEach(n => {
            if (n.querySelector(':scope > ul.menu-tree > li')) n.classList.add('is-collapsed');
        }));
    document.getElementById('btnExpandAll').addEventListener('click', () =>
        root.querySelectorAll('.menu-node').forEach(n => n.classList.remove('is-collapsed')));

    // ---- quick edit --------------------------------------------------------
    root.addEventListener('click', function (e) {
        const li = e.target.closest('.menu-node');
        if (!li) return;

        if (e.target.closest('.js-quick-edit')) {
            const panel = li.querySelector(':scope > .menu-node__quick');
            panel.hidden = !panel.hidden;
            return;
        }
        if (e.target.closest('.js-qe-cancel')) {
            li.querySelector(':scope > .menu-node__quick').hidden = true;
            return;
        }
        if (e.target.closest('.js-qe-save')) {
            const panel = li.querySelector(':scope > .menu-node__quick');
            const btn = e.target.closest('.js-qe-save');
            const payload = {
                nama: panel.querySelector('.js-qe-nama').value,
                icon: panel.querySelector('.js-qe-icon').value,
                is_active: panel.querySelector('.js-qe-active').checked ? 1 : 0,
            };
            btn.disabled = true;
            fetch(panel.dataset.quickUrl, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify(payload),
            })
            .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
            .then(res => {
                const m = res.menu;
                li.querySelector(':scope > .menu-node__row .js-node-name').textContent = m.nama;
                const iconEl = li.querySelector(':scope > .menu-node__row .menu-node__icon i');
                if (iconEl && m.icon) iconEl.className = 'bi ' + m.icon;
                li.querySelector(':scope > .menu-node__row').classList.toggle('is-inactive', !m.is_active);
                panel.hidden = true;
                setStatus('is-saved', '<i class="bi bi-check-circle"></i> ' + T.saved);
            })
            .catch(() => setStatus('is-error', '<i class="bi bi-exclamation-triangle"></i> ' + T.error))
            .finally(() => { btn.disabled = false; });
        }
    });

    // ---- add modal: hide link-only fields for section/divider ------------
    const addType = document.getElementById('addMenuType');
    if (addType) {
        const sync = () => document.querySelectorAll('#addMenuModal .add-link-field')
            .forEach(f => f.style.display = addType.value === 'link' ? '' : 'none');
        addType.addEventListener('change', sync);
        sync();
    }
});
</script>
@endpush
