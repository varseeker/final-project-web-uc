@extends('layouts/layouts-dashboard')

@section('content')
<div class="container">
    <h1>Tambah Produk</h1>
    <form action="{{ route('produk.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
        </div>
        <div class="mb-3">
            <div class="form-group">
                <label for="jenis" class="form-label">Jenis</label>
                <select name="jenis" class="form-control">
                    <option value="Tabungan">Tabungan</option>
                    <option value="Deposito">Deposito</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <input type="textarea" class="form-control" id="deskripsi" name="deskripsi" required>
        </div>
        <div class="mb-3">
            <label for="suku_bunga" class="form-label">Maks Bagi Hasil</label>
            <input type="text" class="form-control" id="suku_bunga" name="suku_bunga" required>
        </div>
        <div class="mb-3">
            <label for="minimum_saldo" class="form-label">Minimum Saldo</label>
            <input type="text" class="form-control" id="minimum_saldo" name="minimum_saldo" required>
        </div>
        <div class="mb-3">
            <label for="biaya_admin" class="form-label">Biaya Admin</label>
            <input type="text" class="form-control" id="biaya_admin" name="biaya_admin" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
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
