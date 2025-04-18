@extends('layouts/layouts-dashboard')

@section('content')
<div class="container">
    <h1>Detail Produk</h1>
    <div class="mb-3">
        <label class="form-label">Nama</label>
        <p class="form-control">{{ $produk->nama }}</p>
    </div>
    <div class="mb-3">
        <label class="form-label">Jenis</label>
        <p class="form-control">{{ $produk->jenis }}</p>
    </div>
    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <p class="form-control">{{ $produk->deskripsi }}</p>
    </div>
    <div class="mb-3">
        <label class="form-label">Maks Bagi Hasil</label>
        <p class="form-control">{{ $produk->suku_bunga }}</p>
    </div>
    <div class="mb-3">
        <label class="form-label">Minimum Saldo</label>
        <p class="form-control">{{ $produk->minimum_saldo }}</p>
    </div>
    <div class="mb-3">
        <label class="form-label">Biaya Admin</label>
        <p class="form-control">{{ $produk->biaya_admin }}</p>
    </div>
    <div class="mb-3">
        <label class="form-label">Tanggal Pembuatan</label>
        <p class="form-control">{{ $produk->created_at }}</p>
    </div>
    <a href="{{ route('produk.index') }}" class="btn btn-primary">Kembali</a>
</div>
@endsection
