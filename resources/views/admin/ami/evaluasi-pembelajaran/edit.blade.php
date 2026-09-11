@extends('layouts.admin')

@section('title', 'Edit Evaluasi Pembelajaran')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Evaluasi Pembelajaran</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.evaluasi-pembelajaran.index') }}">Evaluasi Pembelajaran</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ami.evaluasi-pembelajaran.update', $evaluasiPembelajaran) }}" method="POST">
                @method('PUT')
                @include('admin.ami.evaluasi-pembelajaran._form')
            </form>
        </div>
    </div>
@endsection
