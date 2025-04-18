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
    <h1>Data Produk</h1>
    <a href="{{ route('produk.create') }}" class="btn btn-primary mb-3">Tambah Produk</a>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Deskripsi</th>
                <th>Maks Bagi Hasil</th>
                <th>Minimum Saldo</th>
                <th>Biaya Admin</th>
                <th width="280px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produk as $n)
            <tr>
                <td>{{ $n->id_produk }}</td>
                <td>{{ $n->nama }}</td>
                <td>{{ $n->jenis }}</td>
                <td>{{ $n->deskripsi }}</td>
                <td>{{ $n->suku_bunga }}</td>
                <td>{{ $n->minimum_saldo }}</td>
                <td>{{ $n->biaya_admin }}</td>
                <td>
                    <form action="{{ route('produk.destroy', $n->id_produk) }}" method="POST" style="display:inline;">
                        <a href="{{ route('produk.show', $n->id_produk) }}" class="btn btn-info">Detail</a>
                        <a href="{{ route('produk.edit', $n->id_produk) }}" class="btn btn-warning">Edit</a>
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3">
        {{ $produk->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
