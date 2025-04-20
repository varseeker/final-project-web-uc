@extends('layouts.layouts-main_menu')

@section('content')
<!-- Main Content -->
<main class="container py-4">

  <!-- Top Action Bar -->
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-primary" id="submitOrderBtn" onclick="openSubmitModal()">Submit Order</button>
      <span class="fw-bold">Add Order +</span>
    </div>
    <div class="d-flex align-items-center gap-4 me-4">
      <label class="fw-bold mb-0">Table:</label>
      <input type="text" class="form-control form-control" style="width: 8rem;">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cartModal">Cart <i class="bi bi-cart"></i></button>      
    </div>
  </div>
    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-4 rounded-4">
            <div class="modal-header d-flex justify-content-between align-items-start border-0">
                <h4 class="modal-title fw-bold">Cart</h4>
                <button type="button" class="btn btn-danger rounded-3 px-3 py-1 fw-bold" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <div id="cartList"></div>
                <hr>
                <div class="d-flex justify-content-between align-items-center fw-bold">
                <span>Subtotal</span>
                <span id="subtotal">Rp. 0</span>
                </div>
            </div>
            </div>
        </div>
    </div>
    <hr>
  <!-- Menu Cards -->
  @foreach ($groupedItems as $category => $items)
    <h5 class="fw-bold mb-3">{{ $category }}</h5>
    <div class="row row-cols-md-4 g-3  mb-5">
        @foreach ($items as $index => $item)
        <div class="col">
          <div class="card text-center shadow-sm" style="width: 18rem;">
            <img src="{{ $item['image'] }}" class="img-box bg-light d-flex justify-content-center align-items-center rounded-image-menu" style="height: 200px;" alt="{{ $item['name'] }}">
            <div class="card-body d-flex flex-column justify-content-between">
              <div>
                <h6 class="fw-bold">{{ $item['name'] }}</h6>
                <p class="text-muted mb-2">Rp.{{ number_format($item['price'], 0, ',', '.') }},00</p>
              </div>
              <button class="btn btn-secondary mt-auto"
                @if ($category === 'Coffee')
                  onclick="openDrinkDetailModal('{{ $item['id'] }}', '{{ $item['name'] }}', '{{ $item['price'] }}')"
                @else
                  onclick="addToCart({ id: '{{ $item['id'] }}', name: '{{ $item['name'] }}', price: {{ $item['price'] }} })"
                @endif>
                <i class="bi bi-plus-circle"></i> Add
              </button>
            </div>
          </div>
        </div>
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
        <!-- Inject cart summary here via JS -->
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


<!-- Drink Detail Modal -->
<div class="modal fade" id="drinkDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="drinkDetailForm" class="modal-content p-4 rounded-4">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="drinkDetailTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="fw-bold">Choose Variant</label>
        <select class="form-select mb-4" name="variant">
          <option value="Cold">Cold</option>
          <option value="Hot">Hot</option>
        </select>

        <label class="fw-bold">Size</label>
        <div class="row row-cols-2 g-3 mb-2">
          <div class="form-check ms-3">
            <input class="form-check-input border-dark" type="radio" name="size" value="Reguler" id="size-reguler">
            <label class="form-check-label" for="size-reguler">Reguler</label>
          </div>
          <div class="form-check ms-3">
            <input class="form-check-input border-dark" type="radio" name="size" value="Large" id="size-large">
            <label class="form-check-label" for="size-large">Large</label>
          </div>
        </div>

        <div id="ice-options">
            <label class="fw-bold">Ice</label>
            <div class="row row-cols-2 g-3 mb-2">
                <div class="form-check ms-3">
                    <input class="form-check-input border-dark" type="radio" name="ice" value="Less Ice" id="ice-less">
                    <label class="form-check-label" for="ice-less">Less Ice</label>
                </div>
                <div class="form-check ms-3">
                    <input class="form-check-input border-dark" type="radio" name="ice" value="Normal Ice" id="ice-normal">
                    <label class="form-check-label" for="ice-normal">Normal Ice</label>
                </div>
            </div>
        </div>

        <label class="fw-bold">Sweetness</label>
        <div class="row row-cols-2 g-3">
          <div class="form-check ms-3">
            <input class="form-check-input border-dark" type="radio" name="sugar" value="Less Sugar" id="sugar-less">
            <label class="form-check-label" for="sugar-less">Less Sugar</label>
          </div>
          <div class="form-check ms-3">
            <input class="form-check-input border-dark" type="radio" name="sugar" value="Normal Sugar" id="sugar-normal">
            <label class="form-check-label" for="sugar-normal">Normal Sugar</label>
          </div>
        </div>
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



<script>
  const cart = [];
  renderCart();
  function showToast(message) {
    document.getElementById('toast-message').textContent = message;
    const toast = new bootstrap.Toast(document.getElementById('toastSuccess'));
    toast.show();
  }
  document.querySelector('select[name="variant"]').addEventListener('change', function () {
    const iceSection = document.getElementById('ice-options');
    if (this.value === 'Cold') {
        iceSection.style.display = 'block';
    } else {
        iceSection.style.display = 'none';
        // Optional: uncheck any selected ice option
        document.querySelectorAll('input[name="ice"]').forEach(input => input.checked = false);
    }
  });

  // Hide ice option by default on load (if form shows up with "Hot")
  window.addEventListener('load', function () {
    const variant = document.querySelector('select[name="variant"]').value;
    if (variant !== 'Cold') {
        document.getElementById('ice-options').style.display = 'none';
    }
  });


  function addToCart(item) {
    const existingIndex = cart.findIndex(cartItem =>
        cartItem.id === item.id &&
        cartItem.variant === item.variant &&
        JSON.stringify(cartItem.detail) === JSON.stringify(item.detail)
    );

    if (existingIndex !== -1) {
        // Kalau ada item yang sama, tambahkan quantity-nya saja
        cart[existingIndex].quantity += 1;
    } else {
        // Kalau belum ada, tambahkan item baru ke cart
        cart.push({ ...item, quantity: 1 });
    }
    showToast("Item ditambahkan ke keranjang!");
    renderCart();
  }

  function openDrinkDetailModal(id, name, price) {
    const form = document.getElementById('drinkDetailForm');
    form.dataset.itemId = id;
    form.dataset.itemName = name;
    form.dataset.itemPrice = price;
    document.getElementById('drinkDetailTitle').textContent = name;

    const modal = new bootstrap.Modal(document.getElementById('drinkDetailModal'));
    modal.show();
  }

  document.getElementById('drinkDetailForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const form = e.target;
    const item = {
      id: form.dataset.itemId,
      name: form.dataset.itemName,
      price: parseInt(form.dataset.itemPrice),
      variant: form.variant.value,
      detail: [form.size.value, form.ice.value, form.sugar.value],
      quantity: 1,
    };
    addToCart(item);  
    form.reset();
    bootstrap.Modal.getInstance(document.getElementById('drinkDetailModal')).hide();
  });

  function renderCart() {
    const cartList = document.getElementById('cartList');
    cartList.innerHTML = '';
    let subtotal = 0;

    if (cart.length < 1) {
      cartList.innerHTML = '<p class="text-muted">Your cart is empty.</p>';
    } else {
        cart.forEach((item, index) => {
            subtotal += item.price * item.quantity;
            const details = item.detail ? `<div class="text-muted small">${item.detail.join(', ')}</div>` : '';
            cartList.innerHTML += `
                <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-start">
                    <button class="btn btn-danger rounded p-3 me-3" onclick="removeItem(${index})">-</button>
                    <div>
                    <div><strong>${item.quantity}x</strong> ${item.name}${item.variant ? ' (' + item.variant + ')' : ''}</div>
                    ${details}
                    </div>
                </div>
                <div><strong>Rp. ${item.price.toLocaleString('id-ID')},00</strong></div>
                </div>
            `;
        });
    }
    document.getElementById('subtotal').textContent = `Rp. ${subtotal.toLocaleString('id-ID')},00`;
  }

  function removeItem(index) {
    if (cart[index].quantity > 1) {
        cart[index].quantity -= 1;
    } else {
        cart.splice(index, 1);
    }
    renderCart();
    showToast("Item dihapus dari keranjang!");
  }

  function openSubmitModal() {
    const preview = document.getElementById('cart-summary-preview');
    const finalSubtotal = document.getElementById('finalSubtotal');
    preview.innerHTML = '';
    let subtotal = 0;

    cart.forEach(item => {
        const details = item.detail ? `<div class="text-muted small">${item.detail.join(', ')}</div>` : '';
        subtotal += item.price * item.quantity;

        preview.innerHTML += `
        <div class="d-flex justify-content-between mb-3">
            <div>
            <strong>${item.quantity}x</strong> ${item.name}${item.variant ? ' (' + item.variant + ')' : ''}
            ${details}
            </div>
            <strong>Rp. ${item.price.toLocaleString('id-ID')},00</strong>
        </div>
        `;
    });

    finalSubtotal.textContent = `Rp. ${subtotal.toLocaleString('id-ID')},00`;
    new bootstrap.Modal(document.getElementById('submitOrderModal')).show();
  }

  function submitFinalOrder() {
    renderCart();
    bootstrap.Modal.getInstance(document.getElementById('submitOrderModal')).hide();
    new bootstrap.Modal(document.getElementById('paymentMethodModal')).show();
  }

  document.getElementById('submitOrderBtn').addEventListener('click', () => {
    const cartIsEmpty = cart.length === 0;
    if (cartIsEmpty) {
        showToast('Keranjang kosong!', 'error');
        return;
    }
  });

  function selectPayment(method) {
    bootstrap.Modal.getInstance(document.getElementById('paymentMethodModal')).hide();
    if (method === 'cash') {
        document.getElementById('cashTotalDisplay').textContent = formatCurrency(getCartTotal());
        
        document.getElementById('cashAmount').value = '';
        new bootstrap.Modal(document.getElementById('cashPaymentModal')).show();
    } else if (method === 'qris') {
        document.getElementById('qrisTotalDisplay').innerText = formatCurrency(getCartTotal());
        new bootstrap.Modal(document.getElementById('qrisPaymentModal')).show();
    }
  }

  document.getElementById('cashPaymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const nominal = parseInt(document.getElementById('cashAmount').value);
    const total = getCartTotal();
    if (nominal < total) {
        showToast('Nominal kurang dari total!', 'error');
        return;
    }

    generateReceiptPDF('Tunai', nominal);
    bootstrap.Modal.getInstance(document.getElementById('cashPaymentModal')).hide();
    cart.length = 0;
  });

  function handleQrisSubmit() {
    generateReceiptPDF('Qris');
    bootstrap.Modal.getInstance(document.getElementById('qrisPaymentModal')).hide();
    cart.length = 0;
  }

  function getCartTotal() {
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += item.price * item.quantity;
    });
    return subtotal;
  }

  function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(value);
  }

  function generateReceiptPDF(method, cashReceived = null) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    console.log(doc);
    doc.setFontSize(14);
    doc.text("Underground Cafe", 20, 20);
    doc.text(`Metode: ${method}`, 20, 30);
    let y = 40;

    cart.forEach(item => {
        doc.text(`${item.quantity}x ${item.name} - ${formatCurrency(item.price * item.quantity)}`, 20, y);
        y += 10;
    });

    doc.text(`Subtotal: ${formatCurrency(getCartTotal())}`, 20, y + 10);
    if (method === 'Tunai') {
        doc.text(`Tunai: ${formatCurrency(cashReceived)}`, 20, y + 20);
        doc.text(`Kembalian: ${formatCurrency(cashReceived - getCartTotal())}`, 20, y + 30);
    }

    doc.save("receipt.pdf");

    // Simpan transaksi ke database (sementara dikomentari)
    // axios.post('/api/orders', { cart, paymentMethod: method });
  }
</script>

@endsection