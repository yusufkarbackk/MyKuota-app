@extends('layouts.layouts')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="col-6">
                <h3 class="card-title my-3">Site Details</h3>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Company</th>
                            <th>Nomor</th>
                            <th>Username</th>
                    </thead>
                    </tr>
                    <tbody>
                        @if(!empty($data))
                            <tr>
                                <td>{{ $data['id'] }}</td>
                                <td>{{ $data['site'] }}</td>
                                <td>{{ $data['company'] }}</td>
                                <td>{{ $data['account']['phone_number'] }}</td>
                                <td>{{ $data['account']['username'] }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="5" class="text-center">No clients found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Usage Data
            <div class="col-6 mt-5">
                <h3 class="card-title">Usage Data</h3>
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <td>Month</td>
                            <td>Usage (GB)</td>
                    </thead>
                    </tr>

                </table>
            </div> -->

            <!-- History Data -->
            <div class="col-6 mt-5">
                <h3 class="card-title">Usage History</h3>
                <table class="table">
                    <thead class="table-dark">
                        <td>ID</td>
                        <td>Timestamp</td>
                        <td>Usage</td>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
@endsection