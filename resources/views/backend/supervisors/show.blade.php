@extends('layouts.admin')
@section('content')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex">
        <h6 class="m-0 font-weight-bold text-primary">user ({{ $user->name }})</h6>
        <div class="ml-auto">
            <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                <span class="icon text-white-50">
                    <i class="fa fa-home"></i>
                </span>
                <span class="text">Users</span>
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <tbody>
                <tr>
                    <td colspan="4">
                        @if ($user->user_image != '')
                        <img src="{{ asset('assets/users/' . $user->user_image) }}" width="300" class="rounded-lg">
                        @else
                        <img src="{{ asset('assets/users/defualt.jpg') }}" width="300" class="rounded-lg">
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{ $user->name }}</td>

                    <th>UserName</th>
                    <td>{{ $user->username }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                    <th>Status</th>
                    <td>{{ $user->status() }}</td>

                </tr>
                <tr>
                    <th>Bio</th>
                    <td colspan="3">{{ $user->bio != null ? $user->bio : 'No Bio Info Yet ' }}</td>
                </tr>
                <tr>
                    <th>Post Count</th>
                    <td>{{ $user->posts_count }}</td>

                    <th>Created date</th>
                    <td>{{ $user->created_at->format('d-m-Y h:i a') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection