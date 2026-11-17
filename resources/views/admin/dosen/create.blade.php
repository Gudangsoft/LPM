@extends('layouts.admin')

@section('title', 'Tambah Dosen')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Tambah Dosen</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dosen.index') }}">Data Dosen</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.dosen.store') }}" method="POST">
                @include('admin.dosen._form')
            </form>
        </div>
    </div>
@endsection
