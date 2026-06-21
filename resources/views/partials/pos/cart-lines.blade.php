@php
    $total = 0;
    $showActions = ($mode ?? 'cart') === 'cart';
    $part = $part ?? 'full';
    $isCheckout = ($mode ?? 'cart') === 'checkout';
@endphp

@if($part === 'full' || $part === 'list')
<div class="pos-cart-list">
    @forelse ($baskets as $rock)
        @php $lineTotal = $rock->subtotal ?? ($rock->quantity * $rock->price); $total += $lineTotal; @endphp
        <article class="pos-cart-item">
            <div class="pos-cart-item__main">
                <h6 class="pos-cart-item__title mb-1">{{ $rock->name }}</h6>
                <p class="pos-cart-item__meta small text-muted mb-1">
                    {{ $rock->variant }} · {{ $rock->size }}
                    @if($rock->ice && $rock->ice !== '-')
                        · {{ $rock->ice }}
                    @endif
                    @if($rock->sugar && $rock->sugar !== '-')
                        · {{ $rock->sugar }}
                    @endif
                </p>
                <p class="pos-cart-item__price small mb-0">
                    {{ $rock->quantity }} × Rp{{ number_format($rock->price, 0, ',', '.') }}
                    <span class="fw-bold text-dark ms-1">Rp{{ number_format($lineTotal, 0, ',', '.') }}</span>
                </p>
            </div>
            @if($showActions)
                <div class="pos-cart-item__actions">
                    @if($rock->quantity > 1)
                        <form action="{{ url('/home') }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="update-target" value="{{ $rock->id }}">
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Kurangi">
                                <i class="bi bi-dash-lg"></i>
                            </button>
                        </form>
                    @endif
                    <form action="{{ url('/home') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="delete-target" value="{{ $rock->id }}">
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            @endif
        </article>
    @empty
        <div class="empty-state py-4">
            <i class="bi bi-cart-x"></i>
            <p class="mb-0 fw-semibold">Keranjang masih kosong</p>
            <p class="small mb-0">Pilih menu untuk menambahkan pesanan.</p>
        </div>
    @endforelse
</div>
@endif

@if(($part === 'full' || $part === 'summary') && count($baskets) > 0)
    @php
        if ($part === 'summary') {
            foreach ($baskets as $rock) {
                $total += $rock->subtotal ?? ($rock->quantity * $rock->price);
            }
        }
    @endphp
    @if($isCheckout)
        <div class="pos-cart-summary pos-cart-summary--checkout" data-checkout-summary data-checkout-subtotal="{{ $total }}">
            <div class="pos-cart-summary__row">
                <span>Subtotal</span>
                <strong data-summary-subtotal>Rp{{ number_format($total, 0, ',', '.') }}</strong>
            </div>
            <div class="pos-cart-summary__row text-success" data-summary-discount-row hidden>
                <span>Diskon member (<span data-summary-discount-percent>0</span>%)</span>
                <strong data-summary-discount-amount>- Rp0</strong>
            </div>
            <div class="pos-cart-summary__row pos-cart-summary__row--total">
                <span>Total bayar</span>
                <strong data-summary-grand-total>Rp{{ number_format($total, 0, ',', '.') }}</strong>
            </div>
        </div>
    @else
        <div class="pos-cart-summary">
            <span>Subtotal</span>
            <strong>Rp{{ number_format($total, 0, ',', '.') }}</strong>
        </div>
    @endif
@endif
