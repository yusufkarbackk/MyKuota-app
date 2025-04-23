@extends('layouts.layouts')

@section('title', 'Dashboard')

@section('content')
    <a href="{{ route('sites.manualUpdate') }}">
        <button class="btn btn-primary my-4">Manual Update Sites</button>
    </a>
    <table id="unUpdatedTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Site</th>
                <th>Company</th>
                <th>Username</th>
                <th>Phone Number</th>
                <th>Update Status</th>
                <th>Last Updated</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sites as $site)
                <tr>
                    <td>{{ $site->site }}</td>
                    <td>{{ $site->company }}</td>
                    <td>{{ $site->username }}</td>
                    <td>{{ $site->phone_number }}</td>
                    <td>{{ $site->error_log }}</td>
                    <td>{{ $site->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            $('#unUpdatedTable').DataTable({})
        })
    </script>
@endpush