@extends('layouts.admin')

@section('title', __('admin.users'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.users') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.users') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('admin.user_list') }}</span>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_new') }}
            </a>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('admin.search') }}..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="role">
                        <option value="">{{ __('admin.all_roles') }}</option>
                        @foreach($roles as $roleOption)
                        <option value="{{ $roleOption->slug }}" {{ request('role') == $roleOption->slug ? 'selected' : '' }}>{{ $roleOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i>{{ __('admin.filter') }}
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th width="60">{{ __('admin.avatar') }}</th>
                            <th>{{ __('admin.name') }}</th>
                            <th>{{ __('admin.email') }}</th>
                            <th>{{ __('admin.role') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.registered_at') }}</th>
                            <th width="150">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>
                                @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                @else
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                @endif
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @forelse($user->roles as $userRole)
                                <span class="badge bg-{{ $userRole->slug === 'admin' ? 'danger' : 'info' }}">{{ $userRole->name }}</span>
                                @empty
                                <span class="badge bg-secondary">-</span>
                                @endforelse
                            </td>
                            <td>
                                @if($user->is_active)
                                <span class="badge bg-success">{{ __('admin.active') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('admin.inactive') }}</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
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
            {{ $users->links() }}
        </div>
    </div>
@endsection
