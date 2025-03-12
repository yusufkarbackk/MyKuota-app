@extends('layouts.layouts')

@section('title', 'Create Account')

@section(section: 'content')
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
        <div class="card-body">
            <form method="post" action="/accounts/store">
                @csrf
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Username</label>
                    <input required name="username" type="text" class="form-control" id="exampleInputEmail1"
                        aria-describedby="emailHelp">
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input required name="password" type="text" class="form-control" id="exampleInputPassword1">
                </div>
                <div class="mb-3 form-check">
                    <label for="exampleInputPassword1" class="form-label">Phone Number</label>
                    <input required name="phone_number" type="text" class="form-control" id="exampleInputPassword1">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>


@endsection