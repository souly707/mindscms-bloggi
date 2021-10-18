@extends('layouts/admin')

@section('content')

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">

        <div class="">
            <span class="m-0 font-weight-bold text-primary">Edit Category</span>
        </div>

        <div>
            <a href="{{ route('admin.post_categories.index') }}" class="btn btn-outline-primary">Home <i
                    class="fa fa-home"></i></a>
        </div>

    </div>

    <div class="card-body">

        <div class="card-text px-2 py-2">
            <form action="{{ route('admin.post_categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-8">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $category->name) }}">

                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status"
                                {{ old('status', $category->status) }}>
                                <option value="1">Active</option>
                                <option value="0">Not Active</option>

                                @error('status')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mt-5">
                            <input type="submit" class="btn btn-outline-dark" value="Update Category">
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>

</div>

@endsection