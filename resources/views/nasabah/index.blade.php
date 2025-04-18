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
    <h1>Data Nasabah</h1>
    <!-- <a href="{{ route('nasabah.create') }}" class="btn btn-primary mb-3">Tambah Nasabah</a> -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Nomor Telepon</th>
                <th>Email</th>
                <th>Tanggal Lahir</th>
                <th>Status Pekerjaan</th>
                <th width="280px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nasabah as $n)
            <tr>
                <td>{{ $n->id_nasabah }}</td>
                <td>{{ $n->nama }}</td>
                <td>{{ $n->alamat }}</td>
                <td>{{ $n->nomor_telepon }}</td>
                <td>{{ $n->email }}</td>
                <td>{{ $n->tanggal_lahir }}</td>
                <td>{{ $n->status_pekerjaan }}</td>
                <td>
                    <form action="{{ route('nasabah.destroy', $n->id_nasabah) }}" method="POST" style="display:inline;">
                        <a href="{{ route('nasabah.show', $n->id_nasabah) }}" class="btn btn-info">Detail</a>
                        <a href="{{ route('nasabah.edit', $n->id_nasabah) }}" class="btn btn-warning">Edit</a>
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
        {{ $nasabah->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
