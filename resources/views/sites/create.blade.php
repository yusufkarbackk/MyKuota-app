@extends('layouts.layouts')

@section('title', 'Create Account')

@section(section: 'content')

    <body>
        <div class="container py-5">
            <div class="col-md-6 mx-auto">
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
                    <div class="card-body">
                        <h3 class="text-center">
                            Create New Site
                        </h3>

                        <form style="display: block;" action="{{ route('sites.store') }}" method="post"
                            id="createClientForm">
                            @csrf
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Site</label>
                                <input required name="site" type="text" class="form-control" aria-describedby="emailHelp">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Select Company:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="company" id="batiFlagRadio"
                                        value="Bati" required>
                                    <label class="form-check-label" for="batiFlagRadio">
                                        Bati
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="company" id="magnetFlagRadio"
                                        value="Magnet" required>
                                    <label class="form-check-label" for="magnetFlagRadio">
                                        Magnet
                                    </label>
                                </div>
                            </div>
                            <div class='mb-3'>
                                <label for="user_id">Select Phone Number:</label>
                                <select name="account_id" id="phoneNumber_id" class="form-control" required>
                                    <option value="">-- Select a Phone Number --</option>

                                    <?php foreach ($data as $number): ?>
                                    <option value="{{ $number->id }}">{{ $number->phone_number }}</option>
                                    <?php endforeach; ?>
                                </select>

                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">save</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <script>
            $(document).ready(function () {
                $('#phoneNumber_id').select2();
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
    </body>
@endsection