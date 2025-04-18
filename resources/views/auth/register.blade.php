@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex justify-content-center">
        <div class="col-md-12">
            <div class="card mx-auto mt-3" style="max-width: 1400px;">
                <div class="row g-0">
                  <div class="col-md-4">
                    <img src="{{asset('img/side-img.png')}}" class="img-fluid rounded-start" alt="...">
                  </div>
                  <div class="col-md-8">
                    <div class="card-body">
                    <div class="card-header fs-4 text-md-center">{{ __('Selamat Datang Nasabah Baru') }}</div>
                        <div class=" px-3 mt-2">
                            <div class="card-body"  data-bs-theme="light">
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                        <label for="noIdentitas" class="fs-6 col-md-8 col-form-label">{{ __('No Identitas / KTP') }}</label>
            
                                            <input id="noIdentitas" type="text" class="form-control @error('noIdentitas') is-invalid @enderror" name="noIdentitas" value="{{ old('noIdentitas') }}" required autocomplete="noIdentitas">
            
                                            @error('noIdentitas')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>            
                                    <div class="row mb-3">
                                        <div class="col-6">
                                        <label for="nama" class="fs-6 col-md-4 col-form-label">{{ __('Nama') }}</label>
            
                                            <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama') }}" required autocomplete="nama" autofocus>
            
                                            @error('nama')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                        <label for="tglLahir" class="fs-6 col-md-8 col-form-label">{{ __('Tanggal Lahir') }}</label>
            
                                            <input id="tglLahir" type="date" class="form-control @error('tglLahir') is-invalid @enderror" name="tglLahir" value="{{ old('tglLahir') }}" required autocomplete="tglLahir">
            
                                            @error('tglLahir')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
            
                                    <div class="row mb-3">
                                        <div class="col-12">
                                        <label for="alamat" class="fs-6 col-md-8 col-form-label">{{ __('Alamat') }}</label>
            
                                            <input id="alamat" type="textarea" class="form-control @error('alamat') is-invalid @enderror" name="alamat" value="{{ old('alamat') }}" required autocomplete="alamat">
            
                                            @error('alamat')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
            
                                    <div class="row mb-3">
                                        <div class="col-12">
                                        <label for="pekerjaan" class="fs-6 col-md-8 col-form-label">{{ __('Status Pekerjaan') }}</label>
            
                                            <input id="pekerjaan" type="text" class="form-control @error('pekerjaan') is-invalid @enderror" name="pekerjaan" value="{{ old('pekerjaan') }}" required autocomplete="pekerjaan">
            
                                            @error('pekerjaan')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
            
                                    <div class="row mb-3">
                                        <div class="col-6">
                                        <label for="noTelepon" class="fs-6 col-md-8 col-form-label">{{ __('Nomor Telepon') }}</label>
            
                                            <input id="noTelepon" type="text" class="form-control @error('noTelepon') is-invalid @enderror" name="noTelepon" value="{{ old('noTelepon') }}" required autocomplete="noTelepon">
            
                                            @error('noTelepon')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label for="email" class="fs-6 col-md-8 col-form-label">{{ __('Email') }}</label>
            
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
            
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror                                            
                                        </div>
                                    </div>
            
                                    <div class="row mb-3">
                                        <div class="col-12">
                                        <label for="password" class="fs-6 col-md-4 col-form-label">{{ __('Password') }}</label>
            
                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    
            
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label for="password-confirm" class="fs-6 col-md-4 col-form-label">{{ __('Confirm Password') }}</label>
            
                                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                        </div>
                                    </div>
            
                                    <div class="row mb-0">
                                        <div class="col-md-6 offset-md-10">
                                            <button type="submit" class="btn btn-primary fs-6">
                                                {{ __('Register') }}
                                            </button>
                                        </div>
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
@endsection
