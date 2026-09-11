@extends('layouts.admin')

@section('title', 'Edit Sasaran Mutu')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Sasaran Mutu</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.sasaran-mutu.index') }}">Sasaran Mutu</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ami.sasaran-mutu.update', $sasaranMutu) }}" method="POST">
                @method('PUT')
                @include('admin.ami.sasaran-mutu._form')
            </form>
        </div>
    </div>
@endsection
