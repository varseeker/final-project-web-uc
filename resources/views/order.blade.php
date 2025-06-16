@extends('layouts.appMidtrans')

@section('content')
<!-- Main Content -->



<script>
    const snapToken = "{{$snapToken}}";
</script>


<main class="container py-4">
  <!-- Top Action Bar -->
  <div class="flex-wrap justify-content-between align-items-center mb-4">
    
  <div class="" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered  ">

            <div class="modal-content p-4 rounded-4">
              <div class="modal-header d-flex justify-content-between align-items-start border-0">

                  <h2 class="modal-title fw-bold">{{$csName}}'s Order</h2>

              </div>
                <hr>

            <div class="modal-body modal-xl px-4">

                @php $total = 0; @endphp
                  @foreach ($baskets as $index => $rock)
                      @php $total += $rock->quantity * $rock->price; @endphp
                      <div class="col" style="margin: 20px 0 0 0;">
                        <div class="card text-left shadow-sm" >

                            <div class="card-body d-flex flex-column justify-content-between">

                              <div>
                                  <h4 class="fw-bold">{{ $rock->name }}</h4>
                                  Variant : <strong>{{ $rock->variant }}</strong> 
                                  | Size : <strong>{{ $rock->size }}</strong> 
                                  | Ice : <strong>{{ $rock->ice }}</strong> 
                                  | Sugar : <strong>{{ $rock->sugar }}</strong>

                                  <p class="text-muted mb-2"> {{ $rock->quantity }} x Rp{{ number_format($rock->price, 0, ',', '.') }} <br> Total : <strong>Rp{{ number_format($rock->subtotal, 0, ',', '.') }}</strong></p>
                              </div>

                            </div>
                            
                        </div>
                      </div>
                  @endforeach
                
            </div>
              <hr class="mt-5">
                <div class="d-flex justify-content-between align-items-center fw-bold ">
                  <h4>Subtotal</h4>
                  <h4 id="subtotal">Rp{{ number_format($total, 0, ',', '.') }}</h4>
                </div>

                <div class="row g-3 align-items-center ms-auto me-auto">

                  <div class="col-auto">
                    <!-- <form action="/home/order" method="POST">
                      <div class="col-auto">
                          <button class="btn btn-outline-success btn-lg" type="submit" > Continue Payment </i></button> 
                      </div>
                    </form>  -->

                  
      <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#paymentMethodModal">Pay Order <i class="bi bi-cart"></i></button>     
                  </div>
            </div>

            
        </div>
      </div>
  </div>
</div>
</div>
<hr>

  <!-- Menu Cards -->


<!-- Submit Confirmation Modal -->
<!-- Modal Payment Method -->
<div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentMethodModalLabel">Payment Method</h5>
                <button type="button" class="btn btn-outline-secondary rounded-3 px-3 py-1 ms-auto" data-bs-dismiss="modal">back</button>
      </div>
      <div class="modal-body row px-5">
        <button class="btn btn-outline-success btn-payment btn-tunai w-100 my-4 me-2 col btn-lg" data-bs-target="#cashPaymentModal" data-bs-toggle="modal"> 
          <i class="bi bi-cash-stack me-2"></i> Cash
        </button>
        <button class="btn btn-outline-info btn-payment btn-qris w-100  my-4 col  btn-lg" id="pay-button">
          <i class="bi bi-qr-code-scan me-2"></i> Qris
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="cashPaymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog  modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Cash Payment</h5>
        <button type="button" 
            class="btn btn-outline-danger rounded-3 px-3 py-1 ms-auto" 
            onclick="location.reload()">
            Cancel Payment
        </button>
      </div>
      <div class="modal-body px-4">

      <!-- <form id="cashPaymentForm"> -->
        <div class="mb-3">
            <h5>Total payment: <span id="cashTotalDisplay" class="fw-bold text-primary">Rp {{ number_format($total, 0, ',', '.') }}</span></h5>
        </div>
        <div class="mb-3">
                    <form action="/print" method="POST">
                      @csrf
            <label for="cashAmount" class="form-label">Insert amount paid :</label>
            <input type="text" class="form-control" id="cashAmount" name="cashAmount" placeholder="{{ number_format($total, 0, ',', '.') }}" required>
            <p id="errorMessage" style="color: red; display: none;"></p>
        </div>
        <div class="text-end">
                    <div class="col-auto">
                    <input type="hidden" name="cartToDelete" class="form-control" value="{{ $orderTarget }}">
                    <input type="hidden" name="customerName" class="form-control" value="{{$csName}}">
                      <button id="tombolBayar" type="submit" class="btn btn-success">Cetak Struk</button>
                    </div>
                  </form> 
        </div>
      <!-- </form> -->

      </div>
    </div>
  </div>
</div>

<!-- <div id="decor-backdrop" class="modal-backdrop fade show" style="display: none"></div> -->

<script>
      document.getElementById('pay-button').onclick = function() {
              snap.pay('{{ $snapToken }}', {
                  onSuccess: function(result) {
                    // window.location.href = "{{ $successUrl }}";
                    fetch("/payment/store", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(result)
                    }).then(response => {
                        
                      // Buat form baru
                      const form = document.createElement('form');
                      form.method = 'POST';
                      form.action = "{{ $successUrl }}";

                      // Tambahkan CSRF token
                      const csrf = document.createElement('input');
                      csrf.type = 'hidden';
                      csrf.name = '_token';
                      csrf.value = '{{ csrf_token() }}';
                      form.appendChild(csrf);

                      
                      const cartToDelete = document.createElement('input');
                      cartToDelete.type = 'hidden';
                      cartToDelete.name = 'cartToDelete';
                      cartToDelete.value = '{{ $orderTarget }}';
                      form.appendChild(cartToDelete);

                      // (Opsional) Tambahkan data lain dari result
                      const orderIdInput = document.createElement('input');
                      orderIdInput.type = 'hidden';
                      orderIdInput.name = 'order_id';
                      orderIdInput.value = result.order_id;
                      form.appendChild(orderIdInput);

                      // Tambahkan form ke body dan submit
                      document.body.appendChild(form);
                      form.submit();

                    })
                  },
                  onPending: function(result) {
                      // Tangani jika pembayaran pending
                  },
                  onError: function(result) {
                      // Tangani jika pembayaran gagal
                  }
              });
          };

    document.addEventListener('DOMContentLoaded', function () {
        const tombolBayar = document.getElementById('tombolBayar');
        const cashAmountInput = document.getElementById('cashAmount');
        const cashTotalDisplay = document.getElementById('cashTotalDisplay');
        const errorMessage = document.getElementById('errorMessage');

        // Ambil nilai total dari display & parsing ke angka
        const total = parseFloat(
            cashTotalDisplay.textContent
                .replace('Rp', '')
                .replace(/\./g, '') // hapus titik
                .trim()
        );

        // Format input ke bentuk Rupiah saat user mengetik
        cashAmountInput.addEventListener('keyup', function () {
            const rawValue = this.value.replace(/\D/g, ''); // Hanya angka
            this.value = formatRupiah(rawValue, ''); // Format tampilannya

            const paid = parseInt(rawValue || 0);
            if (paid >= total) {
                tombolBayar.disabled = false;
                errorMessage.style.display = 'none';
            } else {
                tombolBayar.disabled = true;
                errorMessage.textContent = 'Uang yang dibayarkan belum cukup.';
                errorMessage.style.display = 'block';
            }
        });

        // Prevent submit jika tetap dipaksa
        tombolBayar.addEventListener('click', function (event) {
            const rawValue = cashAmountInput.value.replace(/\D/g, '');
            const paid = parseInt(rawValue || 0);

            if (isNaN(paid) || paid < total) {
                event.preventDefault();
                errorMessage.textContent = 'Uang yang dibayarkan tidak boleh kurang dari total tagihan.';
                errorMessage.style.display = 'block';
            }
        });

        // Fungsi format ke Rupiah
        function formatRupiah(angka, prefix) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix === undefined ? rupiah : (rupiah ? prefix + rupiah : '');
        }

        // Awal: disable tombol dulu
        tombolBayar.disabled = true;
    });
</script>
@endsection
