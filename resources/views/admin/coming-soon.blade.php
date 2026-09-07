@extends('layouts.admin')

@section('title', $title)

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ $title }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-cone-striped text-warning" style="font-size: 3.5rem;"></i>
            <h4 class="mt-3 mb-2">{{ $title }}</h4>
            <p class="text-muted mb-4">
                {{ __('admin.feature_in_development') }}
            </p>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left me-1"></i>{{ __('admin.back') }} ke Dashboard
            </a>
        </div>
    </div>
@endsection
