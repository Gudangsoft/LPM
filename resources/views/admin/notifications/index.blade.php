@extends('layouts.admin')

@section('title', __('admin.notifications'))

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ __('admin.notifications') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">{{ __('admin.notifications') }}</li>
                </ol>
            </nav>
        </div>
        @if($notifications->where('read_at', null)->count() > 0)
        <form action="{{ route('admin.notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-check2-all me-1"></i>{{ __('admin.mark_all_read') }}
            </button>
        </form>
        @endif
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <tbody>
                        @forelse($notifications as $notif)
                        <tr class="{{ $notif->read_at ? '' : 'fw-semibold' }}">
                            <td>
                                <a href="{{ route('admin.notifications.read', $notif->id) }}" class="text-decoration-none text-body">
                                    {{ $notif->data['message'] ?? 'Notifikasi' }}
                                </a>
                            </td>
                            <td class="text-muted small text-nowrap">{{ $notif->created_at->diffForHumans() }}</td>
                            <td class="text-end" style="width: 100px;">
                                @if($notif->read_at)
                                <span class="badge bg-secondary">{{ __('admin.read') }}</span>
                                @else
                                <span class="badge bg-danger">{{ __('admin.unread') }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">
                                <i class="bi bi-bell-slash fs-1 text-muted d-block mb-2"></i>
                                {{ __('admin.no_notifications') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($notifications->hasPages())
        <div class="card-footer">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
@endsection
