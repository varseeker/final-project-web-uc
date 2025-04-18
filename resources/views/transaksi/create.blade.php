@extends('layouts/layouts-dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Tambah Transaksi</h2>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{-- <strong>Whoops!</strong> There were some problems with your input.<br><br> --}}
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>
    @endif

    <form action="{{ route('transaksi.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label for="nomor_rekening">Rekening :</label>
                    <select name="nomor_rekening" class="form-control" required>
                        <option value="" disabled selected>Select Rekening Nasabah</option>
                        @foreach($rekenings as $rekening)
                            <option value="{{ (string)$rekening->nomor_rekening }}">{{ $rekening->nomor_rekening }} || {{ $rekening->nasabah->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Bank Tujuan:</strong>
                    <select name="bank_tujuan" id="bank_tujuan" class="form-select" required onchange="toggleRekeningValidation()">
                        <option value="">-- Pilih Bank --</option>
                        <option value="Banksie">Banksie</option>
                        <option value="Lainnya">Bank Lainnya</option>
                    </select>
                </div>
            </div>     
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>No Rekening Tujuan:</strong>
                    <input type="text" name="nomor_rekening_tujuan" id="nomor_rekening_tujuan" class="form-control" required>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Jenis Transaksi:</strong>
                    <select name="jenis_transaksi" id="jenis_transaksi" class="form-select">
                        <option value="Top Up/Beli">Top Up</option>
                        <option value="Payment/Jual">Payment</option>
                    </select>
                </div>
            </div>   
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Jumlah Transaksi:</strong>
                    <input type="text" id="jumlah_transaksi" name="jumlah_transaksi" class="form-control" placeholder="Jumlah Transaksi" required>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('jumlah_transaksi').addEventListener('input', function (e) {
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
    

    function toggleRekeningValidation() {
            const bankTujuan = document.getElementById('bankTujuan').value;
            const noRekening = document.getElementById('nomor_rekening');
            
            if (bankTujuan === 'banksie') {
                noRekening.addEventListener('blur', validateRekening);
            } else {
                noRekening.removeEventListener('blur', validateRekening);
            }
        }

        function validateRekening() {
            const noRekening = document.getElementById('nomor_rekening').value;

            if (noRekening.trim() === '') {
                return;
            }

            fetch(`/cek-rekening/${noRekening}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.message) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Rekening Tidak Ditemukan',
                            text: 'Nomor rekening yang Anda masukkan tidak terdaftar.',
                        }).then(() => {
                            document.getElementById('nomor_rekening').value = '';
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
</script>
@endsection
