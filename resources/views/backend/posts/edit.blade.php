@extends('layouts/admin')

@section('content')

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">

        <div class="">
            <span class="m-0 font-weight-bold text-primary">Edit Posts</span>
        </div>

        <div>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-primary">Home <i
                    class="fa fa-home"></i></a>
        </div>

    </div>

    <div class="card-body">

        <div class="card-text px-2 py-2">
            <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ old('title', $post->title) }}">

                            @error('title')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control" rows="4"
                                id="description">{{ old('description', $post->description) }}</textarea>

                            @error('description')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="category">Chose Category</label>
                            <select class="form-control" name="category" id="category"
                                {{ old('category', $post->category_id) }}>
                                @foreach ($categories as $key => $value)
                                <option value="{{ $value }}">{{ $key }}</option>
                                @endforeach

                                @error('category')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="comment_able">Comments Able</label>
                            <select class="form-control" name="comment_able" id="comment_able"
                                {{ old('comment_able', $post->comment_able) }}>
                                <option value="1">Yes</option>
                                <option value="0">No</option>

                                @error('comments_able')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status" {{ old('status', $post->status) }}>
                                <option value="1">Active</option>
                                <option value="0">Not Active</option>

                                @error('status')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </select>
                        </div>
                    </div>
                </div>

                @if (count($post->media) > 0)
                <div class="row mt-2">
                    @foreach ($post->media as $media)
                    <div class="col-4">
                        <img class="img-thumbnail" width="500" src="{{ asset('assets/posts/'. $media->file_name) }}"
                            alt="{{ $post->title }}">
                        <a href="{{ route('admin.posts.media.destroy', $media->id) }}" id="post-media-{{ $media->id }}"
                            class="btn btn-outline-danger btn-block">Delete</a>
                    </div>
                    @endforeach
                </div>
                @endif
                <div class="row mt-2">
                    <div class="col-12">

                        <div class="custom-file">
                            <input type="file" name="images[]" class="custom-file-input" id="post-images" multiple>
                            <label class="custom-file-label" for="post-images">Choose Image</label>

                            @error('images')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mt-5">
                            <input type="submit" class="btn btn-outline-dark" value="Update Post">
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>

</div>

@endsection