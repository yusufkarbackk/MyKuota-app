@extends('layouts.layouts')

@section('title', content: 'Dashboard')

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
                    <h3 class="card-title">Accounts Dashboard</h3>
                </div>
                <div class="col-md-6 text-end">
                    <a href="/accounts/createCSV">
                        <button class="btn btn-primary">Create Account CSV</button>
                    </a>
                    <a href="/accounts/create">
                        <button class="btn btn-primary">Create Account</button>
                    </a>
                </div>
            </div>
        </div>
        <table id="accountsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Phone Number</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accounts as $account)
                    <tr>
                        <td>{{ $account->username }}</td>
                        <td>{{ $account->phone_number }}</td>
                        <td>{{ $account->status }}</td>
                        <td>
                            <form action="{{ route('accounts.destroy', $account->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this account?');"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                            <a href="{{ route('accounts.edit', $account->id) }}"><button
                                    class="btn btn-secondary">Edit</button></a>
                            <form action="{{ route('accounts.terminate', $account->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to terminate this account?');"
                                style="display: inline;">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-warning">Terminate</button>
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