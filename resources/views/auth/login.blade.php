@extends('layouts.login')

@section('content')
<div class="limiter">
    <div class="container-login100" style="background-image: url('{{ asset('images/vs1.jpg') }}');">
      <div class="wrap-login100">
        <form class="login100-form validate-form" method="POST" action="{{ route('login') }}">
            @csrf
            <span class="login100-form-logo">
                <i class="zmdi zmdi-accounts"></i>
            </span>
  
          <span class="login100-form-title p-b-34 p-t-27">{{ config('app.name', 'Laravel') }}</span>
  
          <div class="wrap-input100 validate-input {{ $errors->has('email') ? ' is-invalid' : '' }}" data-validate = "Enter email">
            <input class="input100" type="email" name="email" placeholder="Email" value="{{ old('email') }}">
            <span class="focus-input100" data-placeholder="&#xf207;"></span>
          </div>
          @if ($errors->has('email'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
  
          <div class="wrap-input100 validate-input {{ $errors->has('password') ? ' is-invalid' : '' }}" data-validate="Enter password">
            <input class="input100" type="password" name="password" placeholder="Password">
            <span class="focus-input100" data-placeholder="&#xf191;"></span>
            @if ($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
          </div>
  
          <div class="contact100-form-checkbox">
            <input class="input-checkbox100" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="label-checkbox100" for="remember">
              Remember me
            </label>
          </div>
  
          <div class="container-login100-form-btn">
            <button type="submit" class="login100-form-btn">
                {{ __('Login') }}
            </button>
          </div>
  
          <div class="text-center p-t-90">
            @if (Route::has('password.request'))
                <a class="txt1" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
