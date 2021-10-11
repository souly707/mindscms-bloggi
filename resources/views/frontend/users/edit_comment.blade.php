@extends('layouts.app')

@section('content')


<div class="col-lg-9 col-12">

    <div class="card mt-4">
        <div class="card-body">
            <div class="card-title  border-bottom px-2 py-2">
                <h5>Edit Comment</h5>
            </div>

            <div class="card-text px-2 py-2">
                <form action="{{ route('user.comment.update', $comment->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $comment->name }}">

                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $comment->email }}">

                                @error('email')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="url">Website</label>
                                <input type="text" name="url" class="form-control" value="{{ $comment->url }}">

                                @error('url')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="comment">Comment</label>
                                <textarea name="comment" class="form-control" id="description"
                                    rows="5">{{$comment->comment}}</textarea>

                                @error('comment')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mt-5">
                                <input type="submit" class="btn btn-outline-dark" value="Update Comment">
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