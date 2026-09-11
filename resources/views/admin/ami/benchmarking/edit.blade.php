@extends('layouts.admin')

@section('title', 'Edit Benchmarking')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Benchmarking</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.benchmarking.index') }}">Benchmarking</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ami.benchmarking.update', $benchmarking) }}" method="POST">
                @method('PUT')
                @include('admin.ami.benchmarking._form')
            </form>
        </div>
    </div>
@endsection
