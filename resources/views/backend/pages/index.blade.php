@extends('layouts/admin')

@section('content')

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">

        <div class="">
            <span class="m-0 font-weight-bold text-primary">Mange pages</span>
        </div>

        <div>
            <a href="{{ route('admin.pages.create') }}" class="btn btn-outline-primary">Create page <i
                    class="fa fa-plus"></i></a>
        </div>

    </div>
    {{-- Filter Filds --}}

    @include('backend.pages.filter.filter')

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>User</th>
                        <th>Created At</th>
                        <th class="text-center" style="">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pages as $page)
                    <tr>
                        <td><a href="{{ route('admin.pages.show', $page->id) }}">{{ $page->title }}</a></td>

                        <td>{{ $page->status() }}</td>
                        <td><a
                                href="{{ route('admin.pages.index', ['category_id' => $page->category_id]) }}">{{ $page->category->name }}</a>
                        </td>
                        <td>{{ $page->user->name }}</td>
                        <td>{{ $page->created_at->format('d-m-Y h:i a') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.pages.edit', $page->id) }}"
                                    class="btn btn-outline-dark btn-sm">Edit</a>

                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm ml-2"
                                    onclick="if(confirm('Are you sure to delete this page')){document.getElementById('delete-page-{{ $page->id }}').submit(); }else {return false}">
                                    Delete</a>
                            </div>
                        </td>
                    </tr>
                    <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST"
                        id="delete-page-{{ $page->id }}" style="display: hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No pages Found</td>
                    </tr>

                    @endforelse

                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">
                            <div class="float-right">{{ $pages->appends(request()->input())->links() }}</div>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection