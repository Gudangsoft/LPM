@extends('layouts.admin')

@section('title', __('admin.menu_management'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.menu_management') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.menu_management') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('admin.menu_list') }}</span>
            <a href="{{ route('admin.menu.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_new') }}
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="60">{{ __('admin.order') }}</th>
                            <th>{{ __('admin.name') }}</th>
                            <th>{{ __('admin.type') }}</th>
                            <th>{{ __('admin.icon') }}</th>
                            <th>{{ __('admin.route') }}</th>
                            <th>{{ __('admin.parent') }}</th>
                            <th width="80">{{ __('admin.status') }}</th>
                            <th width="150">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $menu)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $menu->urutan }}</span>
                            </td>
                            <td>
                                @if($menu->icon)
                                <i class="bi {{ $menu->icon }} me-2"></i>
                                @endif
                                {{ $menu->nama }}
                            </td>
                            <td>
                                @switch($menu->tipe)
                                    @case('link')
                                        <span class="badge bg-primary">Link</span>
                                        @break
                                    @case('section')
                                        <span class="badge bg-info">Section</span>
                                        @break
                                    @case('divider')
                                        <span class="badge bg-secondary">Divider</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($menu->icon)
                                <i class="bi {{ $menu->icon }} fs-5"></i>
                                <code class="ms-2">{{ $menu->icon }}</code>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($menu->route)
                                <code>{{ $menu->route }}</code>
                                @elseif($menu->url)
                                <small>{{ $menu->url }}</small>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($menu->parent)
                                    <span class="badge bg-secondary">{{ $menu->parent->nama }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($menu->is_active)
                                <span class="badge bg-success">{{ __('admin.active') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('admin.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.menu.edit', $menu) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.menu.destroy', $menu) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $menus->links() }}
        </div>
    </div>
@endsection
