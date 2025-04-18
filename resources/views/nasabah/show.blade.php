@extends('layouts/layouts-dashboard')

@section('content')
<div class="container">
    <h1>Detail Nasabah</h1>
    <div class="mb-3">
        <label class="form-label">Nama</label>
        <p class="form-control">{{ $nasabah->nama }}</p>
    </div>
    <div class="mb-3">
        <label class="form-label">Alamat</label>
        <p class="form-control">{{ $nasabah->alamat }}</p>
    </div>
    <div class="mb-3">
        <label class="form-label">Nomor Telepon</label>
        <p class="form-control">{{ $nasabah->nomor_telepon }}</p>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <p class="form-control">{{ $nasabah->email }}</p>
    </div>
    <div class="mb-3">
        <label class="form-label">Tanggal Lahir</label>
        <p class="form-control">{{ $nasabah->tanggal_lahir }}</p>
    </div>
    <div class="mb-3">
        <label class="form-label">Status Pekerjaan</label>
        <p class="form-control">{{ $nasabah->status_pekerjaan }}</p>
    </div>
    <a href="{{ route('nasabah.index') }}" class="btn btn-primary">Kembali</a>
</div>
@endsection
