@extends('layouts.admin')

@section('title', 'Edit Hasil Survei')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Hasil Survei</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.survey-kepuasan.index') }}">Survey Kepuasan</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ami.survey-kepuasan.update', $surveyKepuasan) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.ami.survey-kepuasan._form')
            </form>
        </div>
    </div>
@endsection
