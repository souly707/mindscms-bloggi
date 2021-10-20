@extends('layouts/admin')

@section('content')

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">

        <div class="">
            <span class="m-0 font-weight-bold text-primary">Create Page</span>
        </div>

        <div>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-primary">Home <i
                    class="fa fa-home"></i></a>
        </div>

    </div>

    <div class="card-body">

        <div class="card-text px-2 py-2">
            <form action="{{ route('admin.pages.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}">

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
                                id="description">{{ old('description') }}</textarea>

                            @error('description')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category">Chose Category</label>
                            <select class="form-control" name="category" id="category" {{ old('category') }}>
                                @foreach ($categories as $key => $value)
                                <option value="{{ $value }}">{{ $key }}</option>
                                @endforeach

                                @error('category')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status" {{ old('status') }}>
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
                    <div class="col-12">

                        <div class="custom-file">
                            <input type="file" name="images[]" class="custom-file-input" id="page-images" multiple>
                            <label class="custom-file-label" for="page-images">Choose Image</label>

                            @error('images')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mt-5">
                            <input type="submit" class="btn btn-outline-dark" value="Craete page">
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>

</div>

@endsection