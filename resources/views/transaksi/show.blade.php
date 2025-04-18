@extends('layouts/layouts-dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Show Transaksi</h2>
            <a class="btn btn-primary" href="{{ route('transaksi.index') }}"> Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Nomor Rekening Asal:</strong>
                {{ $resultTransaksi->nomor_rekening_asal }} - {{ $resultTransaksi->nama }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Nomor Rekening Tujuan:</strong>
                {{ $resultTransaksi->nomor_rekening_tujuan }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Bank Tujuan:</strong>
                {{ $resultTransaksi->bank_tujuan }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Jenis Transaksi:</strong>
                {{ $resultTransaksi->jenis_transaksi }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Jumlah Transaksi:</strong>
                {{ $resultTransaksi->jumlah_transaksi }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Tanggal Transaksi:</strong>
                {{ $resultTransaksi->created_at }}
            </div>
        </div>
    </div>
</div>
@endsection
