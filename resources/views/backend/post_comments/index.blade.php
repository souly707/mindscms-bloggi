@extends('layouts/admin')

@section('content')

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">

        <div class="">
            <span class="m-0 font-weight-bold text-primary">Mange Comments</span>
        </div>

    </div>
    {{-- Filter Filds --}}

    @include('backend.post_comments.filter.filter')

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Author</th>
                        <th width="40%">Comment</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th class="text-center" style="">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($comments as $comment)
                    <tr>
                        <td><img src="{{ asset('assets/posts/default-sm.jpg') }}" width="60" class="rounded-lg"></td>

                        <td>
                            {{ $comment->name }}
                            @if ($comment->user_id != '')
                            <span class="text-info">Member</span>
                            @endif

                            @if ($comment->url != '')
                            <a href="{{ $comment->url }}" target="_blank">Website</a>
                            @endif
                        </td>

                        <td>
                            {{ $comment->comment }}
                            <div class="text-muted">
                                <a href="{{ route('admin.posts.show', $comment->post->id) }}">{{ $comment->post->title }}
                                </a>
                            </div>
                        </td>

                        <td>{{ $comment->status() }}</td>

                        <td>{{ $comment->created_at->format('d-m-Y h:i a') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.post_comments.edit', $comment->id) }}"
                                    class="btn btn-outline-dark btn-sm">Edit</a>

                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm ml-2"
                                    onclick="if(confirm('Are you sure to delete this comment')){document.getElementById('delete-comment-{{ $comment->id }}').submit(); }else {return false}">
                                    Delete</a>
                            </div>
                        </td>
                    </tr>
                    <form action="{{ route('admin.post_comments.destroy', $comment->id) }}" method="POST"
                        id="delete-comment-{{ $comment->id }}" style="display: hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No Comments Found</td>
                    </tr>

                    @endforelse

                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">
                            <div class="float-right">{{ $comments->appends(request()->input())->links() }}</div>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection