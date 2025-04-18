@extends('layouts/layouts-dashboard')

@section('content')
<div class="container">
    <h2>Add Rekening</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('rekening.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="id_nasabah">Nasabah:</label>
            <select name="id_nasabah" class="form-control" required>
                <option value="" disabled selected>Select Nasabah</option>
                @foreach($nasabahs as $nasabah)
                    <option value="{{ (string)$nasabah->id_nasabah }}">{{ $nasabah->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="jenis_rekening">Jenis Rekening:</label>
            <input type="text" name="jenis_rekening" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="saldo">Saldo:</label>
            <input type="text" name="saldo" id="saldo" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="tanggal_pembukaan">Tanggal Pembukaan:</label>
            <input type="date" name="tanggal_pembukaan" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>

<script>
document.getElementById('saldo').addEventListener('input', function (e) {
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
