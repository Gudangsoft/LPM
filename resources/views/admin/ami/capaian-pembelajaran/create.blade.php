@extends('layouts.admin')

@section('title', 'Tambah Capaian Pembelajaran')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Tambah Capaian Pembelajaran</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.capaian-pembelajaran.index') }}">Capaian Pembelajaran</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ami.capaian-pembelajaran.store') }}" method="POST">
                @include('admin.ami.capaian-pembelajaran._form')
            </form>
        </div>
    </div>
@endsection
