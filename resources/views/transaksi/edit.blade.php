@extends('layouts/layouts-dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Edit Transaksi</h2>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transaksi.update', $transaksi->id_transaksi) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Nomor Rekening:</strong>
                    <input type="text" name="nomor_rekening" value="{{ $transaksi->nomor_rekening }}" class="form-control" placeholder="Nomor Rekening">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Jenis Transaksi:</strong>
                    <input type="text" name="jenis_transaksi" value="{{ $transaksi->jenis_transaksi }}" class="form-control" placeholder="Jenis Transaksi">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Tanggal Transaksi:</strong>
                    <input type="date" name="tanggal_transaksi" value="{{ $transaksi->tanggal_transaksi }}" class="form-control" placeholder="Tanggal Transaksi">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Jumlah Transaksi:</strong>
                    <input type="text" name="jumlah_transaksi" value="{{ $transaksi->jumlah_transaksi }}" class="form-control" placeholder="Jumlah Transaksi">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection
