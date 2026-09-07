{{--
    Recursive public navbar item.
    Expects: $item (Menu), $depth (0 = top level)
--}}
@php
    $depth = $depth ?? 0;
    $kids = $item->relationLoaded('activeChildrenRecursive') ? $item->activeChildrenRecursive : collect();
    $hasKids = $kids->isNotEmpty();
    $maxChildDepth = \App\Models\Menu::MAX_DEPTH - 1;

    $url = $item->getUrl();
    $attr = $item->buka_tab ? ' target="_blank" rel="noopener"' : '';
    $active = $item->isBranchActive();
@endphp

@if($depth === 0)
    @if($hasKids)
        @php $nestedSubmenu = $kids->contains(fn ($c) => $c->relationLoaded('activeChildrenRecursive') && $c->activeChildrenRecursive->isNotEmpty()); @endphp
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ $active ? 'active' : '' }}" href="#" role="button"
               data-bs-toggle="dropdown" data-bs-display="static" {{ $nestedSubmenu ? 'data-bs-auto-close=outside' : '' }} aria-expanded="false">{{ $item->nama }}</a>
            <ul class="dropdown-menu">
                @foreach($kids as $child)
                    @include('partials.frontend-menu-item', ['item' => $child, 'depth' => 1])
                @endforeach
            </ul>
        </li>
    @else
        <li class="nav-item">
            <a class="nav-link {{ $active ? 'active' : '' }}" href="{{ $url }}"{!! $attr !!}>{{ $item->nama }}</a>
        </li>
    @endif
@else
    @if($hasKids && $depth < $maxChildDepth)
        <li class="dropdown-submenu">
            <a class="dropdown-item {{ $active ? 'active' : '' }}" href="{{ $url }}"{!! $attr !!}>{{ $item->nama }}</a>
            <ul class="dropdown-menu">
                @foreach($kids as $child)
                    @include('partials.frontend-menu-item', ['item' => $child, 'depth' => $depth + 1])
                @endforeach
            </ul>
        </li>
    @else
        <li>
            <a class="dropdown-item {{ $item->isActive() ? 'active' : '' }}" href="{{ $url }}"{!! $attr !!}>{{ $item->nama }}</a>
        </li>
    @endif
@endif
