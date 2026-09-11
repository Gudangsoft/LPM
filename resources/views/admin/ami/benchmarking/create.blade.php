@extends('layouts.admin')

@section('title', 'Tambah Benchmarking')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Tambah Benchmarking</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.benchmarking.index') }}">Benchmarking</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ami.benchmarking.store') }}" method="POST">
                @include('admin.ami.benchmarking._form')
            </form>
        </div>
    </div>
@endsection
