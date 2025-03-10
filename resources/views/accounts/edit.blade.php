@extends('layouts.layouts')

@section('title', content: 'Dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Account</h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('accounts.update', $account->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input name="phone_number" type="text" class="form-control" id="phone_number"
                        value="{{ old('phone_number', $account->phone_number) }}" required>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input name="username" type="text" class="form-control" id="username"
                        value="{{ old('username', $account->username) }}" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">New Password (Leave blank to keep current password)</label>
                    <input name="password" type="password" class="form-control" id="password">
                </div>

                <button type="submit" class="btn btn-primary">Update Account</button>
            </form>
        </div>
    </div>
@endsection