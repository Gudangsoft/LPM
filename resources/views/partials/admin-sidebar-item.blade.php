{{--
    Recursive sidebar menu item.
    Expects: $item (Menu), $depth (0 = top level), $authUser (User|null)
--}}
@php
    $authUser = $authUser ?? auth()->user();
    $visible = ! $authUser || $item->isVisibleTo($authUser);
    $kids = $item->relationLoaded('activeChildrenRecursive') ? $item->activeChildrenRecursive : collect();
@endphp

@if($visible)
    @php $badgeCount = $item->getBadgeCount(); @endphp
    <a href="{{ $item->getUrl() }}"
       class="nav-link {{ $depth === 1 ? 'nav-link--child' : ($depth >= 2 ? 'nav-link--grandchild' : '') }} {{ $item->isActive() ? 'active' : '' }}">
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

    @if($depth < (\App\Models\Menu::MAX_DEPTH - 1) && $kids->isNotEmpty())
        @foreach($kids as $child)
            @include('partials.admin-sidebar-item', ['item' => $child, 'depth' => $depth + 1, 'authUser' => $authUser])
        @endforeach
    @endif
@endif
