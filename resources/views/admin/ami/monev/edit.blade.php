@extends('layouts.admin')

@section('title', 'Edit Monev')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Monev</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.monev.index') }}">Monev</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ami.monev.update', $monev) }}" method="POST">
                @method('PUT')
                @include('admin.ami.monev._form')
            </form>
        </div>
    </div>
@endsection
