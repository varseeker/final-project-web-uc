@extends('layouts.app')

@php
    $cartItemCount = (int) ($cartCount ?? ($baskets ?? collect())->sum('quantity'));
    $hasCartItems = $cartItemCount > 0 || count($baskets ?? []) > 0;
@endphp
@push('body-attrs')
data-cart-count="{{ $cartItemCount }}"
@endpush

@section('content')
<main class="container py-4 pb-pos-dock">

    <!-- <script>
        $(document).ready(function () {
          showToast('pesannya disini');
        });
    </script> -->
    
@if(session()->has('lastAct'))
    <script>
        $(document).ready(function () {
          $('#cartModal').modal('show');

          showToast('{{session('lastAct')}}');
        });
    </script>
@endif

<script>
    function showToast(message) {
        document.getElementById('toast-message').textContent = message;
        const toast = new bootstrap.Toast(document.getElementById('toastSuccess'));
        toast.show();
    }
</script>

<div class="position-fixed bottom-0 end-0 p-3 pos-toast-wrap">
    <div id="toastSuccess" class="toast align-items-center toast-themed border-0" role="alert" aria-live="polite">
        <div class="d-flex">
            <div class="toast-body" id="toast-message">Berhasil</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

{{-- Floating POS dock --}}
@php
    $dockTotal = ($baskets ?? collect())->sum(fn ($b) => $b->subtotal ?? ($b->quantity * $b->price));
@endphp
<div class="pos-dock" role="toolbar" aria-label="Keranjang dan pesanan">
    <div class="pos-dock__info d-none d-sm-block">
        <span class="pos-dock__label">Total</span>
        <strong class="pos-dock__total">Rp{{ number_format($dockTotal, 0, ',', '.') }}</strong>
    </div>
    <div class="pos-dock__actions">
        <button type="button" class="btn btn-outline-light pos-dock__btn" data-bs-toggle="modal" data-bs-target="#cartModal" aria-label="Buka keranjang">
            <i class="bi bi-cart3"></i>
            <span class="d-none d-md-inline">Keranjang</span>
            <span class="badge rounded-pill bg-danger cart-badge ms-1" data-cart-badge>0</span>
        </button>
        <button type="button" class="btn btn-success pos-dock__btn pos-dock__btn--primary" data-bs-toggle="modal" data-bs-target="#orderModal" aria-label="Konfirmasi pesanan" @if(!$hasCartItems) disabled @endif>
            <i class="bi bi-check2-circle"></i>
            <span class="d-none d-md-inline">Konfirmasi</span>
        </button>
    </div>
</div>

{{-- Cart modal --}}
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable pos-modal">
        <div class="modal-content pos-modal__content">
            <div class="modal-header pos-modal__header">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="cartModalLabel">Keranjang</h5>
                    <p class="small text-muted mb-0">Kelola item sebelum konfirmasi</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body pos-modal__body">
                @include('partials.pos.cart-lines', ['baskets' => $baskets, 'mode' => 'cart'])
            </div>
            @if($hasCartItems)
            <div class="modal-footer pos-modal__footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Lanjut belanja</button>
                <button type="button" class="btn btn-success" data-open-order-modal>
                    Konfirmasi pesanan
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Confirm order modal --}}
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable pos-modal">
        <div class="modal-content pos-modal__content">
            <div class="modal-header pos-modal__header">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="orderModalLabel">Konfirmasi Pesanan</h5>
                    <p class="small text-muted mb-0">Periksa pesanan dan nama pelanggan</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body pos-modal__body">
                @include('partials.pos.cart-lines', ['baskets' => $baskets, 'mode' => 'checkout'])
            </div>
            @if($hasCartItems)
            <div class="modal-footer pos-modal__footer border-0 flex-column align-items-stretch gap-3">
                <form action="{{ url('/home/order') }}" method="POST" class="pos-checkout-form">
                    @csrf
                    <label for="customerName" class="form-label fw-semibold mb-1">Nama pelanggan</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="customerName" id="customerName" class="form-control form-control-lg" placeholder="Contoh: Budi" required autocomplete="name">
                        <button type="submit" class="btn btn-success btn-lg">
                            Bayar <i class="bi bi-arrow-right-short"></i>
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

    @include('partials.pos.menu-catalog', ['menuCatalog' => $menuCatalog ?? null])

</main>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitOrderModal" tabindex="-1" aria-labelledby="submitOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 p-4">
      <h4 class="fw-bold mb-4">Cart</h4>

      <div class="mb-4" id="cart-summary-preview">
        
      </div>

      <hr class="my-3">

      <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
        <span>Subtotal</span>
        <span id="finalSubtotal">Rp. 0,00</span>
      </div>

      <div class="d-flex justify-content-end gap-3">
        <button class="btn btn-danger px-4 py-2 rounded-pill fw-bold" data-bs-dismiss="modal">Batal</button>
        <button id="confirmSubmitOrder" class="btn btn-primary px-4 py-2 rounded-pill fw-bold" onclick="submitFinalOrder()">Continue</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal Payment Method -->
<div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentMethodModalLabel">Payment Method</h5>
      </div>
      <div class="modal-body">
        <button class="btn btn-block btn-payment btn-tunai w-100 mb-3" onclick="selectPayment('cash')">
          <i class="bi bi-cash-stack me-2"></i> Tunai
        </button>
        <button class="btn btn-block btn-payment btn-qris w-100" onclick="selectPayment('qris')">
          <i class="bi bi-qr-code-scan me-2"></i> Qris
        </button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="cashPaymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Pembayaran Tunai</h5>
      </div>
      <div class="modal-body">
      <form id="cashPaymentForm">
        <div class="mb-3">
            <h5>Total yang harus dibayar: <span id="cashTotalDisplay" class="fw-bold text-primary">Rp0</span></h5>
        </div>
        <div class="mb-3">
            <label for="cashAmount" class="form-label">Masukkan jumlah uang:</label>
            <input type="number" class="form-control" id="cashAmount" placeholder="0" required>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-success">Cetak Struk</button>
        </div>
      </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="qrisPaymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Pembayaran Qris</h5>
      </div>
      <div class="modal-body text-center">
        <img src="{{ asset('img/dummy-qr.svg') }}" alt="QRIS" class="img-fluid mb-3" width="200">
        <p>Total yang harus dibayar:</p>
        <h4 class="fw-bold text-primary" id="qrisTotalDisplay">Rp 0</h4>
        <button class="btn btn-dark w-100 mt-3" onclick="handleQrisSubmit()">Submit & Cetak Struk</button>
      </div>
    </div>
  </div>
</div>

<div id="decor-backdrop" class="modal-backdrop fade show" style="display: none"></div>

@endsection
