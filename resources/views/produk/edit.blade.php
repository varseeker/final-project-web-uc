@extends('layouts/layouts-dashboard')

@section('content')
<div class="container">
    <h1>Edit Produk</h1>
    <form action="{{ route('produk.update', $produk->id_produk) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ $produk->nama }}" required>
        </div>
        <div class="mb-3">
            <label for="jenis" class="form-label">Jenis</label>
            <textarea class="form-control" id="jenis" name="jenis" required>{{ $produk->jenis }}</textarea>
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <input type="text" class="form-control" id="deskripsi" name="deskripsi" value="{{ $produk->deskripsi }}" required>
        </div>
        <div class="mb-3">
            <label for="suku_bunga" class="form-label">Maks Bagi Hasil</label>
            <input type="text" class="form-control" id="suku_bunga" name="suku_bunga" value="{{ $produk->suku_bunga }}" required>
        </div>
        <div class="mb-3">
            <label for="minimum_saldo" class="form-label">Minimum Saldo</label>
            <input type="number" class="form-control" id="minimum_saldo" name="minimum_saldo" value="{{ $produk->minimum_saldo }}" required>
        </div>
        <div class="mb-3">
            <label for="biaya_admin" class="form-label">Biaya Admin</label>
            <input type="text" class="form-control" id="biaya_admin" name="biaya_admin" value="{{ $produk->biaya_admin }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
<script>
    document.getElementById('minimum_saldo').addEventListener('input', function (e) {
        let value = e.target.value.replace(/,/g, '').replace(/[^\d.]/g, '');
        if (!isNaN(value) && value !== '') {
            let formattedValue = parseFloat(value).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            e.target.value = formattedValue;
        } else {
            e.target.value = '';
        }
    });
    document.getElementById('biaya_admin').addEventListener('input', function (e) {
        let value = e.target.value.replace(/,/g, '').replace(/[^\d.]/g, '');
        if (!isNaN(value) && value !== '') {
            let formattedValue = parseFloat(value).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            e.target.value = formattedValue;
        } else {
            e.target.value = '';
        }
    });
</script>
@endsection
