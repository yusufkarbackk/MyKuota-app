@extends('layouts.layouts')

@section('title', 'Dashboard')

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
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <h3 class="card-title">Sites Dashboard</h3>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary">Create Sites CSV</button>
                    <button class="btn btn-primary">Create site</button>
                    <button class="btn btn-primary">Check Not Updated Sites</button>
                </div>
            </div>
        </div>
        <table id="accountsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Site</th>
                    <th>Company</th>
                    <th>Phone Number</th>
                    <th>Quota</th>
                    <th>Quota Usage</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sites as $site)
                    <tr>
                        <td>{{ $site->site }}</td>
                        <td>{{ $site->company }}</td>
                        <td>{{ $site->account->phone_number }}</td>
                        <td>{{ $site->account->quota }}</td>
                        <td>{{ $site->usage }}</td>
                        <td>{{ $site->account->updated_at }}</td>
                        <td><a href=""><button class="btn btn-danger">Delete</button></a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            console.log("initilize data tables")
            $('#accountsTable').DataTable({
                layout: {
                    topStart: {
                        buttons: [{
                            extend: 'csv',
                            className: 'btn btn-success',
                            fieldSeparator: ';'
                        }, 'excel', 'pdf']
                    }
                },
            });
        });
    </script>
@endpush