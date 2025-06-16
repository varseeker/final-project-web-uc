@extends('layouts.app')

@section('content')
<!-- Main Content -->
<main class="container py-4">

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

  <!-- Top Action Bar -->
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

  <!-- order modal Bar -->
    <div class="d-flex align-items-center gap-4 me-4">
      <!-- <label class="fw-bold mb-0">Table:</label>
      <input type="text" class="form-control form-control" style="width: 8rem;"> -->
      <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#orderModal">Confirm Order <i class="bi bi-cart"></i></button>      
       <!-- <button id="openCart-btn" class="btn btn-outline-warning btn-lg" >
          Cart 
       </button> -->

    </div>

    <script>
           function showToast(message) {
              document.getElementById('toast-message').textContent = message;
              const toast = new bootstrap.Toast(document.getElementById('toastSuccess'));
              toast.show();
            }
    </script>

  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
    <div id="toastSuccess" class="toast align-items-center text-bg-dark border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body" id="toast-message">Berhasil</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>

  <div class="modal" id="orderModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">

            <div class="modal-content p-4 rounded-4">
            <div class="modal-header d-flex justify-content-between align-items-start border-0">

                <h4 class="modal-title fw-bold">Confirm Order</h4>
                <button type="button" class="btn btn-danger rounded-3 px-3 py-1 fw-bold" data-bs-dismiss="modal">X</button>

            </div>
                <hr>

            <div class="modal-body">
                <hr style="margin: -30px 0 20px 0;">
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
              <hr>
                <div class="d-flex justify-content-between align-items-center fw-bold">
                  <h4>Subtotal</h4>
                  <h4 id="subtotal">Rp{{ number_format($total, 0, ',', '.') }}</h4>
                </div>

                <div class="row g-3 align-items-center ms-auto me-auto">
                  <div class="col-auto">
                    <label for="inputPassword6" class="col-form-label">Costumer Name</label>
                  </div>
                    <div class="col-auto">
                    <form action="/home/order" method="POST">
                      @csrf
                      <input type="text" name="customerName" class="form-control">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success btn-lg" type="submit" >Confirm Order </i></button> 
                    </div>
                  </form> 
                </div>
                <div class="d-flex ms-auto me-auto justify-content-between">
                      
                </div>
            </div>

            
        </div>
      </div>
    
    <div class="d-flex align-items-center gap-4 me-4">
      <button class="btn btn-outline-warning btn-lg" data-bs-toggle="modal" data-bs-target="#cartModal">Cart <i class="bi bi-cart"></i></button>      
       <!-- <button id="openCart-btn" class="btn btn-outline-warning btn-lg" >
          Cart 
       </button> -->

    </div>
  </div>
  
    <!-- Cart Modal -->

    <div class="modal" id="cartModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">

            <div class="modal-content p-4 rounded-4">
            <div class="modal-header d-flex justify-content-between align-items-start border-0">

                <h4 class="modal-title fw-bold">Cart</h4>
                <button type="button" class="btn btn-danger rounded-3 px-3 py-1 fw-bold" data-bs-dismiss="modal">X</button>

            </div>
                <hr>

            <div class="modal-body">
                <hr style="margin: -20px 0 20px 0;">
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

                                  <p class="text-muted mb-2"> {{ $rock->quantity }} x Rp{{ number_format($rock->price, 0, ',', '.') }}</p>
                              </div>
                                <div class="d-flex justify-content-end align-items-center fw-bold pe-3">
                                  <form action="/home" method="POST">                             
                                                  @if($rock->quantity != 1)

                                                  <form action="/home" method="POST">
                                                      @csrf
                                                      @method('put')
                                                      <input type="hidden" name="update-target" value="{{$rock->id}}">
                                                      <button id="update" type="submit" class="btn btn-outline-warning">Kurangi</button>
                                                  </form>


                                                  @endif

                                                  <form action="/home" method="POST">
                                                      @csrf
                                                      @method('DELETE')
                                                      <input type="hidden" name="delete-target" value="{{$rock->id}}">
                                                      <button id="delete" type="submit" class="btn btn-outline-danger ms-3">Hapus</button>
                                                  </form>

                                                   
                                </div>
                            </div>
                            
                        </div>
                      </div>
                  @endforeach
                
            </div>
                <hr>
            <div class="d-flex justify-content-between align-items-center fw-bold">
                <h4>Subtotal</h4>
                <h4 id="subtotal">Rp{{ number_format($total, 0, ',', '.') }}</h4>
                </div>
            </div>
            
        </div>
      </div>
    <hr>

  <!-- Menu Cards -->

  
  @foreach ($groupedItems as $category => $items)
    <h2 class="fw-bold mb-3">{{ $category }}</h2>
    <div class="row row-cols-md-4 g-3  mb-5">
        @foreach ($items as $index => $item)


        

        <div class="col">
            <a   data-bs-toggle="modal"data-bs-target="#drinkDetailModal{{ str_replace(' ', '', $item->name ) }}" >
          <div class="card text-center shadow-sm rounded-image-menu" style="width: 18rem;">
              <img src="{{ $item->img_url }}" class="img-box bg-light d-flex justify-content-center align-items-center rounded-image-menu" style="height: 200px;" alt="{{ $item->name }}">
              <div class="card-body d-flex flex-column justify-content-between border-secondar">
                <div>
                  <h5 class="fw-bold">{{ $item->name }}</h5>
                  <h6 class="text-muted mb-2">Rp{{ number_format($item->price, 0, ',', '.') }}</h6>
                </div>
              </div>
          </div>

            </a>
        </div>

        
        <!-- Drink Detail Modal -->
        <div class="modal" id="drinkDetailModal{{ str_replace(' ', '', $item->name ) }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            
            <form id="drinkDetailForm" class="modal-content p-4 rounded-4" method="POST" action="/home/store">
              @CSRF

              <div class="modal-header border-0">
                <h4 class="modal-title fw-bold" id="drinkDetailTitle">{{ $item->name }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <input type="hidden" name="update_{{$item->id}}" value="{{$item->id}}">
              <!-- <input type="hidden" name="anchor" value="{{$item->id}}"> -->

              <div class="modal-body">
                <label class="fw-bold">Choose Variant</label>
            @if($category != "Snack")
                <select class="form-select mb-4" name="variant-{{ $item->id }}">
                  <option value="Hot">Hot</option>
                  <option value="Cold">Cold</option>
                </select>
            @elseif($category == "Snack")

                <select class="form-select mb-4" name="variant-{{ $item->id }}">
                  <option value="Spicy">Spicy</option>
                  <option value="Mild">Mild</option>
                  <option value="Not Spicy">Not Spicy</option>
                </select>
            @endif
                
                <div id="ice-options-{{ $item->id }}" name="ice-name-{{$item->id}}">
                    <label class="fw-bold">Ice</label>
                    <div class="row row-cols-2 g-3 mb-2">
                        <div class="form-check ms-3">
                            <input class="form-check-input border-dark" type="radio" name="ice-{{$item->id}}" value="No Ice" id="ice-no-{{ $item->id }}">
                            <label class="form-check-label" for="ice-no">No Ice</label>
                        </div>
                        <div class="form-check ms-3">
                            <input class="form-check-input border-dark" type="radio" name="ice-{{$item->id}}" value="Less Ice" id="ice-less-{{ $item->id }}">
                            <label class="form-check-label" for="ice-less">Less Ice</label>
                        </div>
                        <div class="form-check ms-3">
                            <input class="form-check-input border-dark" type="radio" name="ice-{{$item->id}}" value="Normal Ice" id="ice-normal-{{ $item->id }}" checked>
                            <label class="form-check-label" for="ice-normal">Normal Ice</label>
                        </div>
                    </div>
                </div>

                <label class="fw-bold">Size</label>
                <div class="row row-cols-2 g-3 mb-2">
                @if($category != "Snack")
                      <div class="form-check ms-3">
                        <input class="form-check-input border-dark" type="radio" name="size-{{$item->id}}" value="Small" id="size-Small">
                        <label class="form-check-label" for="size-Small">Small</label>
                      </div>
                @endif
                  <div class="form-check ms-3">
                    <input class="form-check-input border-dark" type="radio" name="size-{{$item->id}}" value="Reguler" id="size-reguler" checked>
                    <label class="form-check-label" for="size-reguler">Regular</label>
                  </div>
                  <div class="form-check ms-3">
                    <input class="form-check-input border-dark" type="radio" name="size-{{$item->id}}" value="Large" id="size-large">
                    <label class="form-check-label" for="size-large">Large</label>
                  </div>
                </div>


                @if($category != "Snack")
                <label class="fw-bold">Sweetness</label>
                <div class="row row-cols-2 g-3">
                  <div class="form-check ms-3">
                    <input class="form-check-input border-dark" type="radio" name="sugar-{{$item->id}}" value="No Sugar" id="sugar-no">
                    <label class="form-check-label" for="sugar-no">No Sugar</label>
                  </div>
                  <div class="form-check ms-3">
                    <input class="form-check-input border-dark" type="radio" name="sugar-{{$item->id}}" value="Less Sugar" id="sugar-less">
                    <label class="form-check-label" for="sugar-less">Less Sugar</label>
                  </div>
                  <div class="form-check ms-3">
                    <input class="form-check-input border-dark" type="radio" name="sugar-{{$item->id}}" value="Normal Sugar" id="sugar-normal" checked>
                    <label class="form-check-label" for="sugar-normal">Normal Sugar</label>
                  </div>
                </div>
                
                @elseif($category == "Snack")
                  <input type="hidden" name="sugar-{{$item->id}}" value="-">
                @endif
              </div>
              <div class="modal-footer border-0 justify-content-end">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold d-flex align-items-center gap-2">
                  SUBMIT <i class="bi bi-send"></i>
                </button>
              </div>
            </form>

          </div>
        </div>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
          <div id="toastSuccess" class="toast align-items-center text-bg-dark border-0" role="alert">
            <div class="d-flex">
              <div class="toast-body" id="toast-message">Berhasil</div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
          </div>
        </div>

        
        <script>

        $(document).ready(function () {

            function toggleIceOptions() {
                let variant = $('select[name="variant-{{ $item->id }}"]').val();

                if (variant === 'Cold') {
                    $('#ice-options-{{ $item->id }}').show();
                    $('#ice-less-{{ $item->id }}').val("Less Ice");
                    $('#ice-normal-{{ $item->id }}').val("Normal Ice");
                    // $('#ice-normal-{{ $item->id }}').prop('checked');
                } else {
                    $(`#ice-options-{{ $item->id }}`).hide();
                    
                    $('#ice-less-{{ $item->id }}').val("-");
                    $('#ice-normal-{{ $item->id }}').val("-");

                    // Hapus input hidden jika ada
                    // $(`#ice-null-{{ $item->id }}`).remove();
                }
            }

            toggleIceOptions();
            
            $('select[name="variant-{{ $item->id }}"]').on('change', function () {
                toggleIceOptions();
            });
        });

        </script>

      @endforeach
    </div>
    <hr>
  @endforeach

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
        <img src="/images/dummy-qr.png" alt="QRIS" class="img-fluid mb-3" width="200">
        <p>Total yang harus dibayar:</p>
        <h4 class="fw-bold text-primary" id="qrisTotalDisplay">Rp 0</h4>
        <button class="btn btn-dark w-100 mt-3" onclick="handleQrisSubmit()">Submit & Cetak Struk</button>
      </div>
    </div>
  </div>
</div>

<div id="decor-backdrop" class="modal-backdrop fade show" style="display: none"></div>
<script src="../js/update-btn.js"></script>

@endsection
