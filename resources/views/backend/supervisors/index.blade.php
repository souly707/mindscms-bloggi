@extends('layouts/admin')

@section('content')

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">

        <div class="">
            <span class="m-0 font-weight-bold text-primary">Mange Supervisors</span>
        </div>

        <div>
            <a href="{{ route('admin.supervisors.create') }}" class="btn btn-outline-primary">Create Supervisor <i
                    class="fa fa-plus"></i></a>
        </div>

    </div>
    {{-- Filter Filds --}}

    @include('backend.supervisors.filter.filter')

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center">
                        <th>Image</th>
                        <th>Name</th>
                        <th>email</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $user)
                    <tr class="text-center">
                        <td>
                            @if ($user->user_image != null)
                            <img src="{{ asset('assets/users/' . $user->user_image) }}" width="80" class="rounded-lg">
                            @else
                            <img src="{{ asset('assets/users/defualt.jpg') }}" width="80" class="rounded-lg">
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('admin.supervisors.show', $user->id) }}"> {{ $user->name }}</a>
                            <p class="text-gray-400">{{ $user->username }}</p>
                        </td>

                        <td>{{ $user->email }}</td>
                        <td>{{ $user->mobile }}</td>
                        <td>{{ $user->status() }}</td>

                        <td>{{ $user->created_at->format('d-m-Y h:i a') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.supervisors.edit', $user->id) }}"
                                    class="btn btn-outline-dark btn-sm">Edit</a>

                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm ml-2"
                                    onclick="if(confirm('Are you sure to delete this supervisor')){document.getElementById('delete-user-{{ $user->id }}').submit(); }else {return false}">
                                    Delete</a>
                            </div>
                        </td>
                    </tr>
                    <form action="{{ route('admin.supervisors.destroy', $user->id) }}" method="POST"
                        id="delete-user-{{ $user->id }}" style="display: hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No Supervisors Found</td>
                    </tr>

                    @endforelse

                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7">
                            <div class="float-right">{{ $users->appends(request()->input())->links() }}</div>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection