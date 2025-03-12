@extends('layouts.layouts')

@section('title', 'Dashboard')

@section('content')
    <a href="{{ route('sites.unUpdated') }}">
        <button class="btn btn-primary">Manual Update Sites</button>
    </a>
    <table id="accountsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Site</th>
                <th>Company</th>
                <th>Phone Number</th>
                <th>Update Status</th>
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
                            onsubmit="return confirm('Are you sure you want to delete this site?');" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection