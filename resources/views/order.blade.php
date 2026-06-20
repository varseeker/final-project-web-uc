@extends('layouts.appMidtrans')

@section('content')
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
        <button type="button" class="btn btn-outline-info btn-payment btn-qris w-100 my-4 col btn-lg" id="pay-button" @if(empty($snapToken)) disabled @endif>
          <i class="bi bi-qr-code-scan me-2"></i> Qris
        </button>
        @if(empty($snapToken))
          <p class="small text-danger mb-0">Pembayaran QRIS tidak tersedia. Gunakan tunai atau hubungi admin.</p>
        @endif
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

@endsection

@push('scripts')
<script>
(function () {
    const snapToken = @json($snapToken ?? '');
    const successUrl = @json($successUrl);
    const orderTarget = @json($orderTarget);
    const csrfToken = @json(csrf_token());
    const customerName = @json($csName);

    function submitPaymentSuccess(result) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = successUrl;

        const fields = {
            _token: csrfToken,
            cartToDelete: String(orderTarget),
            order_id: result.order_id || result.transaction_id || '-',
            customerName: customerName,
        };

        Object.entries(fields).forEach(function ([name, value]) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    function runSnapPay() {
        if (!snapToken) {
            alert('Pembayaran QRIS belum siap. Silakan gunakan pembayaran tunai.');
            return;
        }

        if (typeof window.snap === 'undefined') {
            alert('Midtrans Snap belum dimuat. Silakan refresh halaman dan coba lagi.');
            return;
        }

        window.snap.pay(snapToken, {
            onSuccess: submitPaymentSuccess,
            onPending: function () {
                alert('Pembayaran masih pending. Silakan selesaikan di aplikasi bank/e-wallet.');
            },
            onError: function () {
                alert('Pembayaran gagal atau dibatalkan. Anda masih bisa membayar tunai.');
            },
            onClose: function () {
                const modalEl = document.getElementById('paymentMethodModal');
                if (modalEl) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            },
        });
    }

    function launchSnapPayment(event) {
        event.preventDefault();

        const modalEl = document.getElementById('paymentMethodModal');
        const modal = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;

        if (modal) {
            modalEl.addEventListener('hidden.bs.modal', function onHidden() {
                modalEl.removeEventListener('hidden.bs.modal', onHidden);
                runSnapPay();
            });
            modal.hide();
            return;
        }

        runSnapPay();
    }

    document.getElementById('pay-button')?.addEventListener('click', launchSnapPayment);

    document.addEventListener('DOMContentLoaded', function () {
        const paymentModalEl = document.getElementById('paymentMethodModal');
        if (paymentModalEl) {
            bootstrap.Modal.getOrCreateInstance(paymentModalEl).show();
        }

        const tombolBayar = document.getElementById('tombolBayar');
        const cashAmountInput = document.getElementById('cashAmount');
        const cashTotalDisplay = document.getElementById('cashTotalDisplay');
        const errorMessage = document.getElementById('errorMessage');

        if (!tombolBayar || !cashAmountInput || !cashTotalDisplay) {
            return;
        }

        const total = parseFloat(
            cashTotalDisplay.textContent
                .replace('Rp', '')
                .replace(/\./g, '')
                .trim()
        );

        cashAmountInput.addEventListener('keyup', function () {
            const rawValue = this.value.replace(/\D/g, '');
            this.value = formatRupiah(rawValue, '');

            const paid = parseInt(rawValue || 0, 10);
            if (paid >= total) {
                tombolBayar.disabled = false;
                errorMessage.style.display = 'none';
            } else {
                tombolBayar.disabled = true;
                errorMessage.textContent = 'Uang yang dibayarkan belum cukup.';
                errorMessage.style.display = 'block';
            }
        });

        tombolBayar.addEventListener('click', function (event) {
            const rawValue = cashAmountInput.value.replace(/\D/g, '');
            const paid = parseInt(rawValue || 0, 10);

            if (isNaN(paid) || paid < total) {
                event.preventDefault();
                errorMessage.textContent = 'Uang yang dibayarkan tidak boleh kurang dari total tagihan.';
                errorMessage.style.display = 'block';
            }
        });

        tombolBayar.disabled = true;

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
    });
})();
</script>
@endpush
