@extends('layouts.admin')

@section('title', 'Edit Dosen')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Dosen</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dosen.index') }}">Data Dosen</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.dosen.update', $dosen) }}" method="POST">
                @method('PUT')
                @include('admin.dosen._form')
            </form>
        </div>
    </div>
@endsection
