@extends('layouts.layouts-dashboard')

@section('content')
<div class="container">
    <h1>Edit Data Diri</h1>
    <form action="{{ route('profile.edit-save', $user->id_nasabah) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- No Identitas -->
            <div class="row mb-3">
                <div class="col-12">
                    <label for="noIdentitas" class="fs-6 col-md-8 col-form-label">{{ __('No Identitas / KTP') }}</label>
                    <input id="noIdentitas" type="text" class="form-control @error('noIdentitas') is-invalid @enderror" 
                        name="noIdentitas" value="{{ old('noIdentitas', $user->noIdentitas) }}" required readonly disabled>
                    @error('noIdentitas')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <!-- Nama dan Tanggal Lahir -->
            <div class="row mb-3">
                <div class="col-6">
                    <label for="nama" class="fs-6 col-md-4 col-form-label">{{ __('Nama') }}</label>
                    <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" 
                        name="nama" value="{{ old('nama', $user->nama) }}" required readonly disabled>
                    @error('nama')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="tglLahir" class="fs-6 col-md-8 col-form-label">{{ __('Tanggal Lahir') }}</label>
                    <input id="tglLahir" type="date" class="form-control @error('tglLahir') is-invalid @enderror" 
                        name="tglLahir" value="{{ old('tglLahir', $user->tanggal_lahir) }}" required readonly disabled>
                    @error('tglLahir')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <!-- Alamat -->
            <div class="row mb-3">
                <div class="col-12">
                    <label for="alamat" class="fs-6 col-md-8 col-form-label">{{ __('Alamat') }}</label>
                    <input id="alamat" type="textarea" class="form-control @error('alamat') is-invalid @enderror" 
                        name="alamat" value="{{ old('alamat', $user->alamat) }}" required readonly disabled>
                    @error('alamat')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <!-- Status Pekerjaan -->
            <div class="row mb-3">
                <div class="col-12">
                    <label for="status_pekerjaan" class="fs-6 col-md-8 col-form-label">{{ __('Status Pekerjaan') }}</label>
                    <input id="status_pekerjaan" type="text" class="form-control @error('status_pekerjaan') is-invalid @enderror" 
                        name="status_pekerjaan" value="{{ old('status_pekerjaan', $user->status_pekerjaan) }}" required >
                    @error('status_pekerjaan')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <!-- Nomor Telepon dan Email -->
            <div class="row mb-3">
                <div class="col-6">
                    <label for="nomor_telepon" class="fs-6 col-md-8 col-form-label">{{ __('Nomor Telepon') }}</label>
                    <input id="nomor_telepon" type="text" class="form-control @error('nomor_telepon') is-invalid @enderror" 
                        name="nomor_telepon" value="{{ old('nomor_telepon', $user->nomor_telepon) }}" required>
                    @error('nomor_telepon')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="email" class="fs-6 col-md-8 col-form-label">{{ __('Email') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                        name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
@endsection
