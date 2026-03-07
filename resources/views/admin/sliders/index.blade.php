@extends('layouts.admin')

@section('title', __('admin.sliders'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.sliders') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.sliders') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('admin.slider_list') }}</span>
            <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_new') }}
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="120">{{ __('admin.image') }}</th>
                            <th>{{ __('admin.title') }}</th>
                            <th>{{ __('admin.link') }}</th>
                            <th>{{ __('admin.order') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th width="150">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sliders as $slider)
                        <tr>
                            <td>
                                <img src="{{ Storage::url($slider->gambar) }}" alt="" class="rounded" style="width: 100px; height: 60px; object-fit: cover;">
                            </td>
                            <td>
                                <strong>{{ $slider->judul ?? '-' }}</strong>
                                @if($slider->deskripsi)
                                <br><small class="text-muted">{{ Str::limit($slider->deskripsi, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($slider->link)
                                <a href="{{ $slider->link }}" target="_blank" class="text-truncate d-block" style="max-width: 200px;">{{ $slider->link }}</a>
                                @else
                                -
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $slider->urutan }}</span></td>
                            <td>
                                @if($slider->is_active)
                                <span class="badge bg-success">{{ __('admin.active') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('admin.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
                            <td colspan="6" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $sliders->links() }}
        </div>
    </div>
@endsection
