@extends('layouts/layouts-dashboard')

@section('content')
    @if(Auth::user()->role === 'admin')
    <div class="row">
    <h1>Dashboard Admin Web Perbankan</h1>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Total Saldo</div>
                <div class="card-body">
                    <p>Rp. {{ number_format($totalSaldo, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Rekening Aktif</div>
                <div class="card-body">
                    <p>{{ $rekeningAktif }} Rekening</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Transaksi Terbaru</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($transaksiTerbaru as $transaksi)
                            <li class="list-group-item">
                                <span>{{ $transaksi->jenis_transaksi }}</span> - Rp. {{ number_format($transaksi->jumlah_transaksi, 0, ',', '.') }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Nasabah Baru</div>
                <div class="card-body">
                    <p>{{ $nasabahBaru }} Nasabah</p>
                    {{-- <a href="{{ route('admin.nasabah') }}" class="btn btn-primary">Lihat Nasabah</a> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row mt-3">
        <div class="col-md-6">
            <h3>Statistik Transaksi Bulanan</h3>
            <canvas id="transaksiBulananChart" width="400" height="200"></canvas>
        </div>
        <div class="col-md-6">
            <h3>Statistik Saldo Per Jenis Rekening</h3>
            <canvas id="saldoRekeningChart" width="400" height="200"></canvas>
        </div>
    </div> -->
    
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.0.0-release/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.0.0-release/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Grafik Transaksi Bulanan
            const ctxTransaksiBulanan = document.getElementById('transaksiBulananChart').getContext('2d');
            new Chart(ctxTransaksiBulanan, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($transaksiBulananLabels) !!},
                    datasets: [{
                        label: 'Jumlah Transaksi',
                        data: {!! json_encode($transaksiBulananData) !!},
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgb(75, 192, 192)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Grafik Saldo Per Jenis Rekening
            const ctxSaldoRekening = document.getElementById('saldoRekeningChart').getContext('2d');
            new Chart(ctxSaldoRekening, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($saldoRekeningLabels) !!},
                    datasets: [{
                        data: {!! json_encode($saldoRekeningData) !!},
                        backgroundColor: ['#f44336', '#e91e63', '#9c27b0', '#673ab7', '#3f51b5'],
                        hoverBackgroundColor: ['#f44336', '#e91e63', '#9c27b0', '#673ab7', '#3f51b5']
                    }]
                }
            });
        });
    </script> -->
    @endif
    @if(Auth::user()->role === 'nasabah')
    <div class="container">
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>
    @endif
    @if ($errors->has('message'))
        <div class="alert alert-danger">
            <p>{{ $errors->first('message') }}</p>
        </div>
    @endif
    <h1>Dashboard Web Perbankan</h1>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahRekeningModal">Tambah Rekening</button>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach ($produkNasabah as $produk)
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $produk->nama }}</h5>
                        <p class="card-text">No Rekening: {{ $produk->nomor_rekening }}</p>
                        <div class="saldo">Saldo: Rp {{ number_format($produk->saldo, 0, ',', '.') }}</div>
                        <div class="transfer-button mt-3">
                            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#historyTrxModal" data-rekening="{{ $produk->nomor_rekening }}">
                                History Transaksi
                            </button>
                            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#transferModal" 
                                onclick="setTransferDetails('{{ $produk->nama }}', '{{ $produk->nomor_rekening }}')">
                                Transfer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferModalLabel">Detail Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('transfer.submitTransfer') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="produk" class="form-label">Produk</label>
                            <input type="text" id="produk" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="nomor_rekening_asal" class="form-label">No Rekening Produk</label>
                            <input type="text" id="nomor_rekening_asal" name="nomor_rekening_asal" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="bankTujuan" class="form-label">Bank Tujuan</label>
                            <select name="bank_tujuan" id="bank_tujuan" class="form-select" required onchange="toggleRekeningValidation()">
                                <option value="">-- Pilih Bank --</option>
                                <option value="Banksie">Banksie</option>
                                <option value="Lainnya">Bank Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nomor_rekening_tujuan" class="form-label">No Rekening</label>
                            <input type="text" name="nomor_rekening_tujuan" id="nomor_rekening_tujuan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal</label>
                            <input type="number" name="nominal" id="nominal" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="historyTrxModal" tabindex="-1" aria-labelledby="historyTrxModalLabel" aria-hidden="true" style="z-index: 2000;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferModalLabel">History Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="row">
                    <div class="col-12" style="padding-left: 2rem; padding-right: 2rem; padding-top: 1rem; padding-bottom: 1rem;">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="historyTrxTable">
                                <thead>
                                    <tr>
                                        <th scope="col" class="sortable" data-sort="nomor">Nomor</th>
                                        <th scope="col" class="sortable" data-sort="nomor_rekening_asal">Nomor Rekening Asal</th>
                                        <th scope="col" class="sortable" data-sort="nomor_rekening_tujuan">Nomor Rekening Tujuan</th>
                                        <th scope="col" class="sortable" data-sort="bank_tujuan">Bank Tujuan</th>
                                        <th scope="col" class="sortable" data-sort="jenis_transaksi">Jenis Transaksi</th>
                                        <th scope="col" class="sortable" data-sort="jumlah_transaksi">Jumlah Transaksi</th>
                                        <th scope="col" class="sortable" data-sort="created_at">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data Transaksi akan dimuat melalui AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Rekening -->
    <div class="modal fade" id="tambahRekeningModal" tabindex="-1" aria-labelledby="tambahRekeningModalLabel" aria-hidden="true" style="z-index: 2000;">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('rekening.addRekening') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahRekeningModalLabel">Tambah Rekening</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_produk" class="form-label">Pilih Produk</label>
                        <select name="id_produk" id="id_produk" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Produk --</option>
                            @foreach ($produkList as $produk)
                                <option value="{{ $produk->id_produk }}" 
                                        data-min-saldo="{{ $produk->minimum_saldo }}" 
                                        data-admin="{{ $produk->biaya_admin }}" 
                                        data-jenis="{{ $produk->jenis }}" 
                                        data-bunga="{{ $produk->suku_bunga }}">
                                    {{ $produk->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Section untuk detail produk -->
                    <div id="produk-detail" style="display: none;">
                        <h6>Detail Produk:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Nama Produk:</strong> <span id="produk-nama"></span></li>
                            <li><strong>Jenis:</strong> <span id="produk-jenis"></span></li>
                            <li><strong>Minimal Saldo:</strong> <span id="produk-min-saldo"></span></li>
                            <li><strong>Admin:</strong> <span id="produk-admin"></span></li>
                            <li><strong>Maks Bagi Hasil:</strong> <span id="produk-bunga"></span></li>
                        </ul>
                    </div>
                        <div class="mb-3">
                            <label for="saldo_awal" class="form-label">Saldo Awal</label>
                            <input type="number" name="saldo_awal" id="saldo_awal" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function setTransferDetails(namaProduk, noRekening) {
            document.getElementById('produk').value = namaProduk;
            document.getElementById('nomor_rekening_asal').value = noRekening;
        }

        function toggleRekeningValidation() {
            const bankTujuan = document.getElementById('bank_tujuan').value;
            const noRekening = document.getElementById('nomor_rekening_tujuan');
            
            if (bankTujuan === 'Banksie') {
                noRekening.addEventListener('blur', validateRekening);
            } else {
                noRekening.removeEventListener('blur', validateRekening);
            }
        }

        function validateRekening() {
            const noRekening = document.getElementById('nomor_rekening_tujuan').value;

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
                            document.getElementById('nomor_rekening_tujuan').value = '';
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        document.addEventListener("DOMContentLoaded", function () {
            const selectProduk = document.getElementById("id_produk");
            const produkDetail = document.getElementById("produk-detail");
            const produkNama = document.getElementById("produk-nama");
            const produkJenis = document.getElementById("produk-jenis");
            const produkMinSaldo = document.getElementById("produk-min-saldo");
            const produkAdmin = document.getElementById("produk-admin");
            const produkBunga = document.getElementById("produk-bunga");

            // Tampilkan detail produk saat pilihan berubah
            selectProduk.addEventListener('change', function () {
                const selectedOption = selectProduk.options[selectProduk.selectedIndex];
                if (selectedOption.value) {
                    const minSaldo = selectedOption.dataset.minSaldo;
                    const admin = selectedOption.dataset.admin;
                    const jenis = selectedOption.dataset.jenis;
                    const bunga = selectedOption.dataset.bunga;
                    const nama = selectedOption.text;

                    produkNama.textContent = nama;
                    produkJenis.textContent = jenis;
                    produkMinSaldo.textContent = `Rp ${parseFloat(minSaldo).toLocaleString()}`;
                    produkAdmin.textContent = `Rp ${parseFloat(admin).toLocaleString()}`;
                    produkBunga.textContent = `${bunga}%`;

                    produkDetail.style.display = 'block';
                } else {
                    produkDetail.style.display = 'none';
                }
            });
        });

        $(document).ready(function() {
            let sortBy = 'created_at'; // Default sort by tanggal
            let sortDirection = 'asc'; // Default sort direction
            let transactionData = []; // Data transaksi yang akan dimuat

            // Fungsi untuk menampilkan data transaksi
            function loadHistoryTransactions(nomorRekening) {
                $.ajax({
                    url: '{{ route("transaksiHistory") }}', // URL untuk mengambil data transaksi
                    method: 'GET',
                    data: {
                        nomor_rekening: nomorRekening
                    },
                    success: function(response) {
                        transactionData = response; // Menyimpan data transaksi yang diterima
                        displayTransactions(); // Tampilkan data yang diterima
                    },
                    error: function(xhr, status, error) {
                        console.error('Terjadi kesalahan saat mengambil data transaksi:', error);
                    }
                });
            }

            // Fungsi untuk menampilkan transaksi di tabel
            function displayTransactions() {
                $('#historyTrxTable tbody').empty(); // Kosongkan tabel sebelumnya
                let nomor = 1; // Inisialisasi nomor urut transaksi
                transactionData.forEach(function(transaction) {
                    let row = '<tr>' +
                        '<td>' + nomor++ + '</td>' +  // Nomor urut transaksi
                        '<td>' + transaction.nomor_rekening_asal + '</td>' +
                        '<td>' + transaction.nomor_rekening_tujuan + '</td>' +
                        '<td>' + transaction.bank_tujuan + '</td>' +
                        '<td>' + transaction.jenis_transaksi + '</td>' +
                        '<td>' + transaction.jumlah_transaksi + '</td>' +
                        '<td>' + transaction.created_at + '</td>' +
                    '</tr>';
                    $('#historyTrxTable tbody').append(row);
                });
            }

            // Menangani event ketika modal dibuka
            $('#historyTrxModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Tombol yang memicu modal
                var nomorRekening = button.data('rekening'); // Ambil nomor rekening dari data-rekening

                // Memuat data transaksi tanpa filter tanggal
                loadHistoryTransactions(nomorRekening);
            });

            // Menangani klik pada header tabel untuk sort
            $('.sortable').on('click', function() {
                var column = $(this).data('sort'); // Ambil nama kolom yang akan disortir
                // Jika kolom yang sama, ubah arah sortirannya
                if (sortBy === column) {
                    sortDirection = (sortDirection === 'asc') ? 'desc' : 'asc';
                } else {
                    sortBy = column;
                    sortDirection = 'asc';
                }

                // Sortir data transaksi di frontend berdasarkan kolom yang dipilih
                transactionData.sort(function(a, b) {
                    if (sortDirection === 'asc') {
                        return a[sortBy] > b[sortBy] ? 1 : -1;
                    } else {
                        return a[sortBy] < b[sortBy] ? 1 : -1;
                    }
                });
                // Menampilkan data yang sudah diurutkan
                displayTransactions();
            });
        });
    </script>
    @endif
@endsection

@section('scripts')
    @if(Auth::user()->role === 'admin')
    @endif
@endsection