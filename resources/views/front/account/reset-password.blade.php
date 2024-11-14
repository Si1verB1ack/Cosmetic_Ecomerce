@extends('front.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item">reset Password</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">
            @include('admin.message')
            <div class="login-form">
                <form action="{{ route('front.processResetPassword') }}" method="post">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <h4 class="modal-title">reset Password</h4>

                    <div class="form-group">
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                            placeholder="New Password" value="{{old('new_password')}}" name="new_password">
                        @error('new_password')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="password" class="form-control @error('confirm_password') is-invalid @enderror"
                            placeholder="Confirm Password" value="" name="confirm_password">
                        @error('confirm_password')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>

                    <input type="submit" class="btn btn-dark btn-block btn-lg" value="Submit">
                </form>
                <div class="text-center small">Go back to login? <a href="{{ route('account.login') }}">Login</a></div>
            </div>
        </div>
    </section>
@endsection
