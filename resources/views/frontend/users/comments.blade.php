@extends('layouts.app')

@section('content')

<!-- Start Blog Area -->

<div class="col-md-9 col-12">

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Post</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($comments as $comment)
                <tr>
                    <td>{{ $comment->name }}</td>
                    <td>{{ $comment->post->title }}</td>
                    <td>{{ $comment->status }}</td>
                    <td class="d-flex justify-content-center align-items-center">
                        <a href="{{ route('user.comment.edit', $comment->id) }}"
                            class="btn-sm btn btn-outline-dark mr-2"><i class="fa fa-edit"></i>
                            Edit</a>

                        <a href="javascript:void(0);" class="btn-sm btn btn-outline-danger"
                            onclick="if (confirm('Are you sure to delete this Comment?')) 
                                        { document.getElementById('post-delete-{{ $comment->id }}').submit(); } else { return false;}">

                            <i class="fa fa-trash"></i> Delete</a>

                        <form action="{{ route('user.comment.destroy', $comment->id) }}" method="POST"
                            id="post-delete-{{ $comment->id }}" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">No Comments Found.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">{{ $comments->links() }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

<div class="col-md-3 col-12 md-mt-40 sm-mt-40">
    {{-- Side Bar  --}}
    @include('partial.frontend.users.sidebar')
</div>

<!-- End Blog Area -->

@endsection