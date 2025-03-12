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
        <div class="container my-5">
            <div class="row justify-content-between">
                <div class="col-3">
                    <h4>Update Status Statistic</h4>
                    <canvas id="myPieChart" width="400" height="400"></canvas>
                </div>

                <div class="col-6">
                    <h4>Sites With Most Usage</h4>
                    <table id="topUsageTable" class="display">
                        <thead>
                            <tr>
                                <td>Site</td>
                                <td>Company</td>
                                <td>Kuota Usage</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topUsage as $data)
                                <tr>
                                    <td>{{$data->site}}</td>
                                    <td>{{$data->company}}</td>
                                    <td>{{$data->usage}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <h3 class="card-title">Sites Dashboard</h3>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('sites.createCSV') }}">
                        <button class="btn btn-primary">Create Sites CSV</button>
                    </a>
                    <a href="{{ route('site.create') }}">
                        <button class="btn btn-primary">Create site</button>
                    </a>
                    <a href="{{ route('sites.unUpdated') }}">
                        <button class="btn btn-primary">Check Not Updated Sites</button>
                    </a>
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
                        <td>
                            <form action="{{ route('sites.delete', $site->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this site?');"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
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
            $('#topUsageTable').DataTable();

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