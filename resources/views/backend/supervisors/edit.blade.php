@extends('layouts/admin')

@section('style')
<link rel="stylesheet" href="{{ asset('backend/vendor/select2/css/select2.min.css') }}">
@endsection

@section('content')

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">

        <div class="text-dark">
            <span class="m-0 font-weight-bold text-primary">Edit User <{{ $user->name }}></span>
        </div>

        <div>
            <a href="{{ route('admin.supervisors.index') }}" class="btn btn-outline-primary">Home <i
                    class="fa fa-home"></i></a>
        </div>

    </div>

    <div class="card-body">

        <div class="card-text px-2 py-2">
            <form action="{{ route('admin.supervisors.update', $user->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row mt-2">
                    <div class="col-4">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">

                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <div class="col-4">
                        <div class="form-group">
                            <label for="username">UserName</label>
                            <input type="text" name="username" class="form-control"
                                value="{{ old('username', $user->username) }}">

                            @error('username')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $user->email) }}">

                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-3">
                        <div class="form-group">
                            <label for="mobile">Mobile</label>
                            <input type="number" name="mobile" class="form-control"
                                value="{{ old('mobile', $user->mobile) }}">

                            @error('mobile')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <div class="col-3">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control">

                            @error('password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="custom-select" name="status" id="status">
                                <option value="---">----</option>
                                <option value="0">Not Active</option>
                                <option value="1">Active</option>
                            </select>
                            @error('status')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="form-group">
                            <label for="receive_email">Receive Email</label>
                            <select class="custom-select" name="receive_email" id="receive_email">
                                <option value="---">----</option>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>

                            @error('receive_email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea name="bio" class="form-control" rows="4">{{ old('bio', $user->bio) }}</textarea>

                            @error('bio')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="form-group">
                            {!! Form::label('permissions', 'permissions') !!}
                            {!! Form::select('permissions[]', [] + $permissions->toArray(), old('permissions',
                            $user_permissions), ['class'
                            => 'form-control select-multiple-tags', 'multiple' => 'multiple']) !!}
                            @error('permissions')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                {{-- <img src="{{ asset('assets/users/' . $user->user_image) }}" alt=""> --}}
                @if ($user->user_image != null)
                <div class="row mt-2">
                    <div class="col-12">
                        <div>
                            <img class="img-thumbnail" width="300"
                                src="{{ asset('assets/users/'. $user->user_image) }}">

                        </div>
                        <div class="mt-2">
                            <a href="{{ route('admin.supervisors.media.destroy', $user->id) }}"
                                id="post-media-{{ $user->id }}" class="btn btn-outline-danger">Delete</a>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row mt-2">
                    <div class="col-12">

                        <div class="custom-file">
                            <input type="file" name="user_image" class="custom-file-input">
                            <label class="custom-file-label" for="user_image">Choose Image</label>

                            @error('user_image')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mt-5">
                            <input type="submit" class="btn btn-outline-dark" value="Update User">
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>

</div>

@endsection

@section('script')
<script src="{{ asset('backend/vendor/select2/js/select2.full.min.js') }}"></script>

<script>
    $(function () {
            $('.select-multiple-tags').select2({
                minimumResultsForSearch: Infinity,
                tags: true,
                closeOnSelect:false
            });
        
        });
</script>
@endsection