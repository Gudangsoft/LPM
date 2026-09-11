@extends('layouts.admin')

@section('title', 'Edit RPS')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit RPS</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.rps-review.index') }}">Review RPS</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ami.rps-review.update', $rpsReview) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.ami.rps-review._form')
            </form>
        </div>
    </div>
@endsection
