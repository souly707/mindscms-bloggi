@extends('layouts.app')

@section('content')

<!-- Start Blog Area -->

<div class="col-lg-9 col-12">

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Comments</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                <tr>
                    <td>{{ $post->title }}</td>
                    <td><a href="{{ route('user.comments', ['post' => $post->id]) }}">{{ $post->comments_count }}</a>
                    </td>
                    <td>{{ $post->status }}</td>
                    <td colspan="0" class="d-flex justify-content-center">
                        <a href="{{ route('user.post.edit', $post->id) }}" class=" btn-sm btn btn-outline-dark mr-2"><i
                                class="fa fa-edit"></i>
                            Edit</a>

                        <a href="javascript:void(0);" class=" btn-sm btn btn-outline-danger"
                            onclick="if (confirm('Are you sure to delete this post?')) 
                                        { document.getElementById('post-delete-{{ $post->id }}').submit(); } else { return false;}">

                            <i class="fa fa-trash"></i> Delete</a>

                        <form action="{{ route('user.post.destroy', $post->id) }}" method="POST"
                            id="post-delete-{{ $post->id }}" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">No Posts Found.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">{{ $posts->links() }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

<div class="col-lg-3 col-12 md-mt-40 sm-mt-40">
    {{-- Side Bar  --}}
    @include('partial.frontend.users.sidebar')
</div>

<!-- End Blog Area -->

@endsection