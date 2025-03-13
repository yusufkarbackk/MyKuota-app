@extends('layouts.layouts')

@section('title', 'Dashboard')

@section('content')
    <div class="container py-5">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center">Edit Client</h3>

                    <form action="{{ route('sites.update', $client->site_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Site -->
                        <div class="mb-3">
                            <label for="site" class="form-label">Site</label>
                            <input required value="{{ old('site', $client->site) }}" name="site" type="text"
                                class="form-control" id="site">
                        </div>

                        <!-- Company -->
                        <div class="mb-3">
                            <label for="company" class="form-label">Company</label>
                            <input required value="{{ old('company', $client->company) }}" name="company" type="text"
                                class="form-control" id="company">
                        </div>

                        <!-- Phone Number Selection -->
                        <div class="mb-3">
                            <label for="phone_number_id" class="form-label">Select Phone Number:</label>
                            <select name="account_id" id="phone_number_id" class="form-control">
                                <option value="{{ $client->account_id ?? '' }}">
                                    {{ $client->phone_number ?? 'Select a phone number' }}
                                </option>
                                @foreach ($accounts as $number)
                                    <option value="{{ $number->id }}">{{ $number->phone_number }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection