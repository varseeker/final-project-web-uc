@extends('layouts/layouts-dashboard')

@section('content')
<div class="container">
    <h2>Rekening Details</h2>
    <table class="table">
        <tr>
            <th>Nomor Rekening:</th>
            <td>{{ $rekening_selected->nomor_rekening }}</td>
        </tr>
        <tr>
            <th>ID Nasabah:</th>
            <td>{{ $rekening_selected->id_nasabah }}</td>
        </tr>
        <tr>
            <th>Nama Nasabah:</th>
            <td>{{ $rekening_selected->nama_nasabah }}</td>
        </tr>
        <tr>
            <th>Nama Produk:</th>
            <td>{{ $rekening_selected->nama }}</td>
        </tr>
        <tr>
            <th>Jenis Rekening:</th>
            <td>{{ $rekening_selected->jenis }}</td>
        </tr>
        <tr>
            <th>Saldo:</th>
            <td>{{ $rekening_selected->saldo }}</td>
        </tr>
        <tr>
            <th>Tanggal Pembukaan:</th>
            <td>{{ $rekening_selected->created_at }}</td>
        </tr>
    </table>
    <a href="{{ route('rekening.index') }}" class="btn btn-primary">Back to List</a>
</div>
@endsection
