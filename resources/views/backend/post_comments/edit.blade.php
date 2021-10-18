@extends('layouts/admin')

@section('content')

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">

        <div class="">
            <span class="m-0 font-weight-bold text-primary">Edit Comment</span>
        </div>

        <div>
            @if ($comment->user_id != '')
            <span class="btn btn-outline-info">Member</span>
            @endif
        </div>

    </div>

    <div class="card-body">

        <div class="card-text px-2 py-2">
            <form action="{{ route('admin.post_comments.update', $comment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $comment->name) }}">

                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $comment->email) }}">

                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group">
                            <label for="url">WebSite</label>
                            <input type="text" name="url" class="form-control" value="{{ old('url', $comment->url) }}">

                            @error('url')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ip_address">Ip Address</label>
                            <input type="text" name="ip_address" class="form-control"
                                value="{{ old('ip_address', $comment->ip_address) }}">

                            @error('ip_address')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status"
                                {{ old('status', $comment->status) }}>
                                <option value="1">Active</option>
                                <option value="0">Not Active</option>

                                @error('status')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="comment">Comment</label>
                            <textarea name="comment" rows="4"
                                class="form-control">{{ old('comment', $comment->comment) }}</textarea>

                            @error('comment')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="form-group mt-5">
                            <input type="submit" class="btn btn-outline-dark" value="Update Comment">
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>

</div>

@endsection