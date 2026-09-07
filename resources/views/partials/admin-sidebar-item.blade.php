{{--
    Recursive sidebar menu item (collapsible groups, up to Menu::MAX_DEPTH levels).
    Expects: $item (Menu), $depth (0 = top level), $authUser (User|null)
--}}
@php
    $authUser = $authUser ?? auth()->user();
    $kids = $item->relationLoaded('activeChildrenRecursive') ? $item->activeChildrenRecursive : collect();
    $isGroup = $kids->isNotEmpty() && $depth < (\App\Models\Menu::MAX_DEPTH - 1);
    $show = $item->isVisibleInTree($authUser);

    $indent = match (true) {
        $depth === 1 => 'nav-link--l2',
        $depth === 2 => 'nav-link--l3',
        $depth >= 3  => 'nav-link--l4',
        default      => '',
    };
    $extAttr = $item->buka_tab ? ' target="_blank" rel="noopener"' : '';
@endphp

@if($show)
    @if($isGroup)
        <div class="nav-group {{ $item->isBranchActive() ? 'is-open' : '' }}" data-menu-id="{{ $item->id }}">
            <a href="#" role="button" class="nav-link nav-group-toggle {{ $indent }}">
                @if($item->icon)
                    <i class="bi {{ $item->icon }}"></i>
                @elseif($depth > 0)
                    <i class="bi bi-dot"></i>
                @endif
                <span>{{ $item->nama }}</span>
                <i class="bi bi-chevron-right nav-group-caret ms-auto"></i>
            </a>
            <div class="nav-group-body">
                @foreach($kids as $child)
                    @include('partials.admin-sidebar-item', ['item' => $child, 'depth' => $depth + 1, 'authUser' => $authUser])
                @endforeach
            </div>
        </div>
    @elseif(blank($item->route) && blank($item->url))
        {{-- container item with nothing to link to yet (e.g. an empty PPEPP stage) --}}
        <span class="nav-link nav-link--muted {{ $indent }}">
            @if($item->icon)
                <i class="bi {{ $item->icon }}"></i>
            @elseif($depth > 0)
                <i class="bi bi-dot"></i>
            @endif
            <span>{{ $item->nama }}</span>
        </span>
    @else
        @php $badgeCount = $item->getBadgeCount(); @endphp
        <a href="{{ $item->getUrl() }}"@if($item->buka_tab) target="_blank" rel="noopener"@endif
           class="nav-link {{ $indent }} {{ $item->isActive() ? 'active' : '' }}">
            @if($item->icon)
                <i class="bi {{ $item->icon }}"></i>
            @elseif($depth > 0)
                <i class="bi bi-dot"></i>
            @endif
            <span>{{ $item->nama }}</span>
            @if($badgeCount > 0)
                <span class="badge {{ $item->badge_class ?? 'bg-danger' }} ms-auto">{{ $badgeCount }}</span>
            @endif
        </a>
    @endif
@endif
