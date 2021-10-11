@extends('layouts.app')

@section('content')


<div class="col-lg-8 offset-md-2">
    <div class="my__account__wrapper">
        <h3 class="account__title">Login</h3>
        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="account__form">

                <div class="input__box">
                    <label>Email <span>*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}">

                    @error('email')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form__btn">
                    <button type="submit">Send Password Reset Link</button>
                </div>

                <a class="forget_pass" href="{{ route('frontend.show_login_form') }}">Go To LogIn?</a>
            </div>

        </form>
    </div>
</div>


@endsection