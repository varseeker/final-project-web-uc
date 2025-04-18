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
    <h2>Rekening List</h2>
    <a href="{{ route('rekening.create') }}" class="btn btn-primary">Add Rekening</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nomor Rekening</th>
                <th>ID Nasabah</th>
                <th>Nama Nasabah</th>
                <th>Jenis Rekening</th>
                <th>Saldo</th>
                <th>Tanggal Pembukaan</th>
                <th width="280px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rekenings as $rekening)
            <tr>
                <td>{{ $rekening->nomor_rekening }}</td>
                <td>{{ $rekening->id_nasabah }}</td>
                <td>{{ $rekening->nama ?? ' - ' }}</td>
                <td>{{ $rekening->jenis }}</td>
                <td>{{ $rekening->saldo }}</td>
                <td>{{ $rekening->created_at }}</td>
                <td>
                    <form action="{{ route('rekening.destroy', $rekening->nomor_rekening) }}" method="POST" style="display:inline-block;">
                        <a href="{{ route('rekening.show', $rekening->nomor_rekening) }}" class="btn btn-info">Detail</a>
                        <a href="{{ route('rekening.edit', $rekening->nomor_rekening) }}" class="btn btn-warning">Edit</a>
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
