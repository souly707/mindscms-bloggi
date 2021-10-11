@extends('layouts.app')

@section('style')
<link rel="stylesheet" href="{{ asset('frontend/js/summernote/summernote-bs4.min.css') }}">

@endsection


@section('content')


<div class="col-lg-9 col-12">

    <div class="card mt-4">
        <div class="card-body">
            <div class="card-title  border-bottom px-2 py-2">
                <h5>Edit Post</h5>
            </div>

            <div class="card-text px-2 py-2">
                <form action="{{ route('user.post.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ $post->title }}">

                        @error('title')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" class="form-control" id="description">
                        {{ $post->description }}</textarea>

                        @error('description')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="category">Chose Category</label>
                                <select class="form-control" name="category" id="category">
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
                                <label for="comments_able">Comments Able</label>
                                <select class="form-control" name="comments_able" id="comments_able">
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
                                <select class="form-control" name="status" id="status">
                                    <option value="1">Active</option>
                                    <option value="0">Not Active</option>

                                    @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="file-loading">

                                <label for="images">Post Image</label>
                                <input type="file" name="images[]" id="post-images" multiple>
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
</div>

<div class="col-lg-3 col-12 md-mt-40 sm-mt-40">
    {{-- Side Bar  --}}
    @include('partial.frontend.users.sidebar')
</div>


@endsection


@section('script')
<script src="{{ asset('frontend/js/summernote/summernote-bs4.min.js') }}"></script>
<script>
    $(function () {
        $('#description').summernote({
            tabSize: 2,
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        $('#post-images').fileinput({
            theme: "fa",
            maxFileCount: 5,
            allowedFileTypes: ['image'],
            showCancel: true,
            showRemove: false,
            showUpload: false,
            overwriteInitial: false,
            initialPreview: [
                @if ($post->media->count() > 0)
                    @foreach ($post->media as $media )
                       "{{ asset('assets/posts/' . $media->file_name) }}",
                    @endforeach
                @endif
            ],
            initialPreviewAsData: true,
            initialPreviewFileType: 'image',
            initialPreviewConfig: [
                
                @if ($post->media->count() > 0)
                    @foreach ($post->media as $media )
                       {
                        caption: "{{ $media->file_name }}",
                        size: "{{ $media->file_size }}",
                        width: "{{ '120px' }}",
                        url: "{{ route('user.post.media.destroy', [$media->id, '_token' => csrf_token()]) }}",
                        key: "{{ $media->id }}"
                       },
                    @endforeach
                @endif
            ],
        });
    });
</script>
@endsection