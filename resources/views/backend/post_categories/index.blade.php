@extends('layouts/admin')

@section('content')

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">

        <div class="">
            <span class="m-0 font-weight-bold text-primary">Mange Posts</span>
        </div>

        <div>
            <a href="{{ route('admin.post_categories.create') }}" class="btn btn-outline-primary">Create Category <i
                    class="fa fa-plus"></i></a>
        </div>

    </div>
    {{-- Filter Filds --}}

    @include('backend.post_categories.filter.filter')

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center">
                        <th>Name</th>
                        <th>Post Count</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th class="text-center" style="">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($categories as $category)
                    <tr class="text-center">
                        <td>{{ $category->name }}</td>
                        <td><a
                                href="{{ route('admin.posts.index', ['category_id' => $category->id]) }}">{{ $category->posts_count }}</a>
                        </td>

                        <td>{{ $category->status() }}</td>

                        <td>{{ $category->created_at->format('d-m-Y h:i a') }}</td>

                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.post_categories.edit', $category->id) }}"
                                    class="btn btn-outline-dark btn-sm">Edit</a>

                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm ml-2"
                                    onclick="if(confirm('Are you sure to delete this category')){document.getElementById('delete-category-{{ $category->id }}').submit(); }else {return false}">
                                    Delete</a>
                            </div>
                        </td>

                    </tr>
                    <form action="{{ route('admin.post_categories.destroy', $category->id) }}" method="POST"
                        id="delete-category-{{ $category->id }}" style="display: hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No Categories Found</td>
                    </tr>

                    @endforelse

                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5">
                            <div class="float-right">{{ $categories->appends(request()->input())->links() }}</div>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection