@extends('layouts.app')

@section('content')
<main class="container py-4">
    @include('layouts.goBack')

    <h1 class="page-header mt-3">Detail Pesanan #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <p class="small text-muted mb-0">Pelanggan</p>
                    <p class="fw-bold mb-0">{{ $order->customer }}</p>
                </div>
                <div class="col-md-6 col-lg-4">
                    <p class="small text-muted mb-0">Kasir</p>
                    <p class="fw-bold mb-0">{{ $order->cashier_name }}</p>
                </div>
                <div class="col-md-6 col-lg-4">
                    <p class="small text-muted mb-0">Referensi Pembayaran</p>
                    <p class="fw-bold mb-0">{{ $order->payReference ?: '-' }}</p>
                </div>
                <div class="col-md-6 col-lg-3">
                    <p class="small text-muted mb-0">Total</p>
                    <p class="fw-bold mb-0">Rp{{ number_format($order->total, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-6 col-lg-3">
                    <p class="small text-muted mb-0">Dibayar</p>
                    <p class="fw-bold mb-0">Rp{{ number_format($order->amountPaid ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-6 col-lg-3">
                    <p class="small text-muted mb-0">Kembalian</p>
                    <p class="fw-bold mb-0">Rp{{ number_format($order->amountChange ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-6 col-lg-3">
                    <p class="small text-muted mb-0">Status</p>
                    <p class="fw-bold mb-0">{{ $order->status }} / {{ $order->{'payment-status'} }}</p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="h5 fw-bold mb-3" style="color: var(--primary-color);">Item yang dipesan</h2>

    <div class="table-responsive-wrap">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Menu</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Variant</th>
                    <th scope="col">Size</th>
                    <th scope="col">Ice</th>
                    <th scope="col">Sugar</th>
                    <th scope="col">Subtotal</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orderItems as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td class="fw-semibold">{{ $item->menu_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp{{ number_format($item->menu_price, 0, ',', '.') }}</td>
                    <td>{{ $item->variant }}</td>
                    <td>{{ $item->size }}</td>
                    <td>{{ $item->ice !== '-' ? $item->ice : '—' }}</td>
                    <td>{{ $item->sugar !== '-' ? $item->sugar : '—' }}</td>
                    <td class="fw-bold">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    <td>{{ $item->status }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">Tidak ada item pada pesanan ini.</td>
                </tr>
                @endforelse
            </tbody>
            @if(count($orderItems) > 0)
            <tfoot>
                <tr class="table-light">
                    <th colspan="8" class="text-end">Total pesanan</th>
                    <th colspan="2">Rp{{ number_format($orderItems->sum('subtotal'), 0, ',', '.') }}</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <div class="mt-3">
        <a href="{{ url('/dashboard/order') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke daftar order
        </a>
    </div>
</main>
@endsection
