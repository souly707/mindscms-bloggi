@extends('layouts.app')

@section('content')


<div class="col-md-8 offset-md-2">
    <div class="my__account__wrapper">
        <h3 class="account__title">Login</h3>
        <form action="{{ route('frontend.login') }}" method="POST">
            @csrf
            <div class="account__form">

                <div class="input__box">
                    <label>Username <span>*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}">

                    @error('username')
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

                <div class="form__btn">
                    <button type="submit">Login</button>

                    <label class="label-for-checkbox">
                        <input class="input-checkbox" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                </div>

                <a class="forget_pass" href="{{ route('password.request') }}">Lost your
                    password?</a>
            </div>

        </form>
    </div>
</div>


@endsection