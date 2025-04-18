@extends('layouts/layouts-dashboard')

@section('content')
<div class="container">
    <h2>Edit Rekening</h2>
    <form action="{{ route('rekening.update', $rekening->nomor_rekening) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="id_nasabah">Nasabah:</label>
            <select name="id_nasabah" class="form-control" required>
                <option value="" disabled>Select Nasabah</option>
                @foreach($nasabahs as $nasabah)
                    <option value="{{ (string)$nasabah->id_nasabah }}" {{ $rekening->id_nasabah == $nasabah->id_nasabah ? 'selected' : '' }}>{{ $nasabah->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="jenis_rekening">Jenis Rekening:</label>
            <input type="text" name="jenis_rekening" class="form-control" value="{{ $rekening->jenis_rekening }}" required>
        </div>
        <div class="form-group">
            <label for="saldo">Saldo:</label>
            <input type="text" name="saldo" id="saldo" class="form-control" value="{{ number_format($rekening->saldo, 2, '.', ',') }}" required>
        </div>
        <div class="form-group">
            <label for="tanggal_pembukaan">Tanggal Pembukaan:</label>
            <input type="date" name="tanggal_pembukaan" class="form-control" value="{{ $rekening->tanggal_pembukaan }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<script>
    document.getElementById('saldo').addEventListener('input', function (e) {
        let value = e.target.value.replace(/,/g, '');
        e.target.value = new Intl.NumberFormat('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
    });
</script>
@endsection
