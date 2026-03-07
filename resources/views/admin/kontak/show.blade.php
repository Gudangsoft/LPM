@extends('layouts.admin')

@section('title', __('admin.view_contact'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.view_contact') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.kontak.index') }}">{{ __('admin.contacts') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.view') }}</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('admin.contact_details') }}</span>
                    @if(!$kontak->is_read)
                    <span class="badge bg-primary">{{ __('admin.unread') }}</span>
                    @else
                    <span class="badge bg-secondary">{{ __('admin.read') }}</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('admin.name') }}</label>
                            <p class="fw-bold">{{ $kontak->nama }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('admin.email') }}</label>
                            <p>
                                <a href="mailto:{{ $kontak->email }}">{{ $kontak->email }}</a>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('admin.phone') }}</label>
                            <p>{{ $kontak->telepon ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('admin.date') }}</label>
                            <p>{{ $kontak->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">{{ __('admin.subject') }}</label>
                        <p class="fw-bold">{{ $kontak->subjek }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">{{ __('admin.message') }}</label>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($kontak->pesan)) !!}
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <a href="mailto:{{ $kontak->email }}?subject=Re: {{ $kontak->subjek }}" class="btn btn-primary">
                            <i class="bi bi-reply me-1"></i>{{ __('admin.reply') }}
                        </a>
                        <a href="{{ route('admin.kontak.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>{{ __('admin.back') }}
                        </a>
                        <form action="{{ route('admin.kontak.destroy', $kontak) }}" method="POST" class="ms-auto" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-1"></i>{{ __('admin.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
