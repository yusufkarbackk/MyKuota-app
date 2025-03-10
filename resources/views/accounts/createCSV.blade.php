@extends('layouts.layouts')

@section('title', 'Create Account')

@section('content')
    <div class="card">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="card-header">
            <h3 class="card-title">Create Account</h3>
        </div>
        <div class="my-5 mx-3">
            <a href="/downloadAccountCSVTemplate" class="btn btn-outline-success">
                Download CSV Template
            </a>
        </div>
        <div class="card-body">
            <form method="post" action="/accounts/storeCSV" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">CSV File</label>
                    <input required name="csv_file" type="file" class="form-control" aria-describedby="emailHelp">
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
@endsection