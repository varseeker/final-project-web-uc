@extends('layouts.app')

@section('content')
<main class="container py-4">
    @include('layouts.goBack')

    <h1 class="page-header mt-3">Detail Pembayaran #{{ str_pad($payment->payment_id, 6, '0', STR_PAD_LEFT) }}</h1>

    {{-- Ringkasan pembayaran --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header fw-bold" style="background: var(--surface-warm); color: var(--primary-color);">
            Informasi Pembayaran
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <p class="small text-muted mb-0">Metode</p>
                    <p class="fw-bold mb-0">{{ $payment->method }}</p>
                </div>
                <div class="col-md-6 col-lg-4">
                    <p class="small text-muted mb-0">Status</p>
                    <p class="fw-bold mb-0">{{ $payment->payment_status }}</p>
                </div>
                <div class="col-md-6 col-lg-4">
                    <p class="small text-muted mb-0">Referensi Transaksi</p>
                    <p class="fw-bold mb-0"><code class="small">{{ $trxRef }}</code></p>
                </div>
                <div class="col-md-6 col-lg-4">
                    <p class="small text-muted mb-0">Order ID</p>
                    <p class="fw-bold mb-0">#{{ str_pad($payment->order_id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="col-md-6 col-lg-4">
                    <p class="small text-muted mb-0">Pelanggan</p>
                    <p class="fw-bold mb-0">{{ $payment->customer ?? '—' }}</p>
                </div>
                <div class="col-md-6 col-lg-4">
                    <p class="small text-muted mb-0">Kasir</p>
                    <p class="fw-bold mb-0">{{ $payment->cashier_name ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Nominal --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm text-center">
                <div class="card-body py-4">
                    <p class="small text-muted mb-1">Total Harga Pesanan</p>
                    <p class="fs-4 fw-bold mb-0" style="color: var(--primary-color);">Rp{{ number_format($orderTotal, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm text-center border-success">
                <div class="card-body py-4">
                    <p class="small text-muted mb-1">Nominal Dibayar</p>
                    <p class="fs-4 fw-bold mb-0 text-success">Rp{{ number_format($amountPaid, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm text-center">
                <div class="card-body py-4">
                    <p class="small text-muted mb-1">Kembalian</p>
                    <p class="fs-4 fw-bold mb-0">{{ $payment->method === 'Cash' ? 'Rp'.number_format($change, 0, ',', '.') : '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail pesanan --}}
    <h2 class="h5 fw-bold mb-3" style="color: var(--primary-color);">Detail Pesanan</h2>

    <div class="table-responsive-wrap">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Menu</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Variant</th>
                    <th>Size</th>
                    <th>Ice</th>
                    <th>Sugar</th>
                    <th>Subtotal</th>
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
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Tidak ada item pesanan.</td>
                </tr>
                @endforelse
            </tbody>
            @if(count($orderItems) > 0)
            <tfoot>
                <tr class="table-light">
                    <th colspan="8" class="text-end">Total item</th>
                    <th>Rp{{ number_format($itemsTotal, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <div class="mt-3 d-flex flex-wrap gap-2">
        <a href="{{ url('/dashboard/payment') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Payment Record
        </a>
        <a href="{{ url('/dashboard/order/'.$payment->order_id) }}" class="btn btn-outline-primary">
            <i class="bi bi-receipt"></i> Lihat Detail Order
        </a>
    </div>
</main>
@endsection
