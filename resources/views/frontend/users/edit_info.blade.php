@extends('layouts.app')

@section('content')



<div class="col-lg-9 col-12">

    <div class="card mt-4">
        <div class="card-body">
            <div class="card-title  border-bottom px-2 py-2">
                <h5>Update Information</h5>
            </div>

            <div class="card-text px-2 py-2">
                <form action="{{ route('user.update_info') }}" id="user_info" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" value="{{auth()->user()->name}}">

                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ auth()->user()->email }}">

                                @error('email')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mobile">Mobile</label>
                                <input type="number" name="mobile" class="form-control"
                                    value="{{ auth()->user()->mobile }}">

                                @error('mobile')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="receive_email">Receive Email</label>
                                <select class="form-control" name="receive_email" id="receive_email">
                                    <option value="{{ auth()->user()->receive_email }}">
                                        {{ auth()->user()->receive_email == 1 ? 'Yes' : 'No' }}</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>

                                    @error('receive_email')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="bio">Bio</label>
                                <textarea name="bio" class="form-control" id="bio"
                                    rows="5">{{auth()->user()->bio}}</textarea>

                                @error('bio')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        @if (auth()->user()->user_image != '')
                        <div class="col-md-12">
                            <img src="{{ asset('assets/users/' . auth()->user()->user_image) }}" class="img-fluid"
                                width="150" alt="{{ auth()->user()->name }}">
                        </div>
                        @endif

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="user_image">User Image</label>
                                <input type="file" name="user_image" class="custom-file" id="user_image">

                                @error('user_image')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group ml-2">
                            <input type="submit" class="btn btn-outline-dark" value="Update Information">
                        </div>

                    </div>
                </form>

            </div>

        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <div class="card-title  border-bottom px-2 py-2">
                <h5>Update Password</h5>
            </div>

            <div class="card-text px-2 py-2">
                <form action="{{ route('user.update_password') }} " id="update_password" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" name="current_password" class="form-control">

                        @error('current_password')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" name="password" class="form-control">

                        @error('password')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Re Password</label>
                        <input type="password" name="password_confirmation" class="form-control">

                        @error('password_confirmation')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-outline-dark" value="Update Password">
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