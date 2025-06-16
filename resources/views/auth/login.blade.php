@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex justify-content-center">
        <div class="col-md-12">
            <div class="card mx-auto mt-3" style="max-width: 800px;">
                <div class="row g-0">
                    
                  <div class="col-md-6">
                    <img src="{{asset('img/side-img.png')}}" class="img-fluid rounded-start rounded-start-login-image" alt="...">
                  </div>
                  <div class="col-md-6 flex row align-items-center">
                    <div class="card-body col ">
                    <div class="fs-4 text-md-center">{{ __('Welcome back!') }}</div>
                        <div class="px-3 mt-2">
                            <div class="card-body"  data-bs-theme="light">
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="row mb-3">
                                        <label for="email" class="fs-6 col-md-6 col-form-label">{{ __('Email Address') }}</label>

                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
            
                                    <div class="row mb-3">
                                        <label for="password" class="fs-6 col-md-6 col-form-label">{{ __('Password') }}</label>

                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>

                                    <div class="row col-md-6 my-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        
                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>
                                    </div>
            
                                    <div class="row mb-0">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Login') }}
                                            </button>
            
                                            @if (Route::has('password.request'))
                                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                                    {{ __('Forgot Your Password?') }}
                                                </a>
                                            @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
