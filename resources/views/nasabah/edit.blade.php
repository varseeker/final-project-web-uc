@extends('layouts/layouts-dashboard')

@section('content')
<div class="container">
    <h1>Edit Nasabah</h1>
    <form action="{{ route('nasabah.update', $nasabah->id_nasabah) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ $nasabah->nama }}" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat" required>{{ $nasabah->alamat }}</textarea>
        </div>
        <div class="mb-3">
            <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
            <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" value="{{ $nasabah->nomor_telepon }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $nasabah->email }}" required>
        </div>
        <div class="mb-3">
            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ $nasabah->tanggal_lahir }}" required>
        </div>
        <div class="mb-3">
            <label for="status_pekerjaan" class="form-label">Status Pekerjaan</label>
            <input type="text" class="form-control" id="status_pekerjaan" name="status_pekerjaan" value="{{ $nasabah->status_pekerjaan }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
@endsection
