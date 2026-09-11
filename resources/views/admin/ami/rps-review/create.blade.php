@extends('layouts.admin')

@section('title', 'Tambah RPS')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Tambah RPS</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.rps-review.index') }}">Review RPS</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ami.rps-review.store') }}" method="POST" enctype="multipart/form-data">
                @include('admin.ami.rps-review._form')
            </form>
        </div>
    </div>
@endsection
