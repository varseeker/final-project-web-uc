@extends('layouts.layouts-dashboard')

@section('content')
<style>
    .pagination-wrapper nav {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .pagination-wrapper .pagination {
        margin: 0;
    }

    .pagination .page-link svg {
        width: 1em;
        height: 1em;
    }

    .pagination .page-item.active .page-link {
        /* background-color: #007bff; */
        /* border-color: #007bff; */
    }

    .pagination .page-link {
        /* color: #007bff; */
    }

    .pagination .page-link:hover {
        /* color: #0056b3; */
    }
</style>


</style>
<div class="container">
    <div class="row">
        <div class="col-lg-12 mb-2 mt-2">
            <h2>Transaksi</h2>
            <a class="btn btn-success" href="{{ route('transaksi.create') }}"> Tambah Transaksi</a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-striped">
        <tr>
            <th>No</th>
            <th>Nomor Rekening Asal</th>
            <th>Nomor Rekening Tujuan</th>
            <th>Bank Tujuan</th>
            <th>Nama Nasabah Pengirim</th>
            <th>Jenis Transaksi</th>
            <th>Jumlah Transaksi</th>
            <th>Tanggal Transaksi</th>
            <th width="280px">Actions</th>
        </tr>
        @foreach ($transaksis as $transaksi)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $transaksi->nomor_rekening_asal }}</td>
            <td>{{ $transaksi->nomor_rekening_tujuan }}</td>
            <td>{{ $transaksi->bank_tujuan }}</td>
            <td>{{ $transaksi->rekening->nasabah->nama ?? ' - ' }}</td>
            <td>{{ $transaksi->jenis_transaksi }}</td>
            <td>{{ $transaksi->jumlah_transaksi }}</td>
            <td>{{ $transaksi->created_at }}</td>
            <td>
                {{-- <form action="{{ route('transaksi.destroy',$transaksi->id_transaksi) }}" method="POST"> --}}
                    <a class="btn btn-info" href="{{ route('transaksi.show',$transaksi->id_transaksi) }}">Show</a>
                    {{-- <a class="btn btn-primary" href="{{ route('transaksi.edit',$transaksi->id_transaksi) }}">Edit</a> --}}
                    {{-- @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button> --}}
                {{-- </form> --}}
            </td>
        </tr>
        @endforeach
    </table>
    <div class="d-flex justify-content-center mt-3">
        {{ $transaksis->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
