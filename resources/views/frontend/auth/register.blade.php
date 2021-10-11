@extends('layouts.app')

@section('content')


<div class="col-lg-8 offset-md-2">
    <div class="my__account__wrapper">
        <h3 class="account__title">Register</h3>
        <form action="{{ route('frontend.register') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="account__form">

                <div class="input__box">
                    <label>Name <span>*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}">

                    @error('name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input__box">
                    <label>Username <span>*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}">

                    @error('username')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input__box">
                    <label>Email <span>*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}">

                    @error('email')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input__box">
                    <label>Mobile <span>*</span></label>
                    <input type="number" name="mobile" value="{{ old('mobile') }}">

                    @error('mobile')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input__box">
                    <label>Password<span>*</span></label>
                    <input type="password" name="password">

                    @error('password')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input__box">
                    <label>Re-Password<span>*</span></label>
                    <input type="password" name="password_confirmation" id="password-confirm">

                    @error('password_confirmation')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input__box">
                    <label>User Image<span></span></label>
                    <input type="file" name="user_image" class="custom-file">

                    @error('user_image')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form__btn">
                    <button type="submit">Create Account</button>
                </div>

                <a class="forget_pass" href="{{ route('frontend.show_login_form') }}">Own an Account Login
                    Now</a>
            </div>

        </form>
    </div>
</div>

@endsection