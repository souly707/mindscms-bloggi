@extends('layouts/admin')

@section('content')

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">

        <div class="">
            <span class="m-0 font-weight-bold text-primary">Mange Posts</span>
        </div>

        <div>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-outline-primary">Create Post <i
                    class="fa fa-plus"></i></a>
        </div>

    </div>
    {{-- Filter Filds --}}

    @include('backend.posts.filter.filter')

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Comments</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>User</th>
                        <th>Created At</th>
                        <th class="text-center" style="">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($posts as $post)
                    <tr>
                        <td><a href="{{ route('admin.posts.show', $post->id) }}">{{ $post->title }}</a></td>
                        <td>{!! $post->comment_able == 1 ? "<a href=\"" . route('admin.post_comments.index',
                                ['post_id'=> $post->id]) . "\">" . $post->comments->count() . "</a>" : 'Disallow' !!}
                        </td>
                        <td>{{ $post->status() }}</td>
                        <td><a
                                href="{{ route('admin.posts.index', ['category_id' => $post->category_id]) }}">{{ $post->category->name }}</a>
                        </td>
                        <td>{{ $post->user->name }}</td>
                        <td>{{ $post->created_at->format('d-m-Y h:i a') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.posts.edit', $post->id) }}"
                                    class="btn btn-outline-dark btn-sm">Edit</a>

                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm ml-2"
                                    onclick="if(confirm('Are you sure to delete this post')){document.getElementById('delete-post-{{ $post->id }}').submit(); }else {return false}">
                                    Delete</a>
                            </div>
                        </td>
                    </tr>
                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST"
                        id="delete-post-{{ $post->id }}" style="display: hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No Posts Found</td>
                    </tr>

                    @endforelse

                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7">
                            <div class="float-right">{{ $posts->appends(request()->input())->links() }}</div>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection