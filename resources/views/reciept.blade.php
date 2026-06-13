<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Struk #{{ $orderId }} — Warkop Kayu</title>
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <style>
    :root {
      --receipt-width: 302px;
      --ink: #2c211c;
      --muted: #6c5f58;
      --line: #e0d6d1;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      padding: 1.5rem 1rem 2rem;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: #ece9e7;
      color: var(--ink);
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
    }

    .receipt-wrap {
      width: 100%;
      max-width: var(--receipt-width);
    }

    .receipt {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 0.5rem;
      padding: 1.25rem 1rem;
      box-shadow: 0 4px 24px rgba(80, 59, 49, 0.12);
    }

    .receipt__brand {
      text-align: center;
      margin-bottom: 0.75rem;
    }

    .receipt__brand h1 {
      font-size: 1.15rem;
      font-weight: 800;
      color: var(--primary-color, #503b31);
      margin: 0 0 0.25rem;
      letter-spacing: 0.04em;
    }

    .receipt__brand p {
      font-size: 0.65rem;
      color: var(--muted);
      line-height: 1.4;
      margin: 0;
    }

    .receipt__meta {
      font-size: 0.72rem;
      line-height: 1.55;
      margin: 0.75rem 0;
      color: var(--ink);
    }

    .receipt__meta strong {
      color: var(--primary-color, #503b31);
    }

    .receipt__divider {
      border: none;
      border-top: 1px dashed var(--line);
      margin: 0.65rem 0;
    }

    .receipt__items {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.72rem;
    }

    .receipt__items thead th {
      text-align: left;
      font-size: 0.65rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--muted);
      padding-bottom: 0.35rem;
      border-bottom: 1px solid var(--line);
    }

    .receipt__items tbody tr + tr td {
      padding-top: 0.5rem;
    }

    .receipt__item-name {
      font-weight: 700;
      color: var(--primary-color, #503b31);
      display: block;
      margin-bottom: 0.15rem;
    }

    .receipt__item-detail {
      font-size: 0.62rem;
      color: var(--muted);
      line-height: 1.35;
    }

    .receipt__items .text-end {
      text-align: right;
      white-space: nowrap;
      vertical-align: top;
    }

    .receipt__totals {
      width: 100%;
      font-size: 0.75rem;
      margin-top: 0.5rem;
    }

    .receipt__totals td {
      padding: 0.2rem 0;
    }

    .receipt__totals .label {
      color: var(--muted);
    }

    .receipt__totals .value {
      text-align: right;
      font-weight: 600;
    }

    .receipt__totals .grand td {
      padding-top: 0.4rem;
      font-size: 0.85rem;
      font-weight: 800;
      color: var(--primary-color, #503b31);
      border-top: 1px solid var(--line);
    }

    .receipt__thanks {
      text-align: center;
      font-size: 0.72rem;
      font-weight: 600;
      color: var(--primary-color, #503b31);
      margin: 0.85rem 0 0;
    }

    .receipt-actions {
      display: flex;
      gap: 0.5rem;
      justify-content: center;
      margin-top: 1rem;
      flex-wrap: wrap;
    }

    .receipt-actions .btn {
      font-size: 0.85rem;
      border-radius: 0.5rem;
      padding: 0.5rem 1rem;
    }

    @media print {
      @page {
        size: 80mm auto;
        margin: 4mm;
      }

      body {
        background: #fff;
        padding: 0;
      }

      .no-print {
        display: none !important;
      }

      .receipt-wrap {
        max-width: 100%;
      }

      .receipt {
        border: none;
        box-shadow: none;
        border-radius: 0;
        padding: 0;
      }
    }
  </style>
</head>
<body>

  <div class="receipt-wrap">
    <article class="receipt">
      <header class="receipt__brand">
        <h1>WARKOP KAYU</h1>
        <p>Jl. Bintaro Permai No. 27<br>
        Kel. Pesanggrahan, Jakarta Selatan 12320</p>
      </header>

      <hr class="receipt__divider">

      <p class="receipt__meta">
        <strong>Tanggal</strong> {{ $orderAt }}<br>
        <strong>No. Order</strong> #{{ str_pad($orderId, 6, '0', STR_PAD_LEFT) }}<br>
        <strong>Pelanggan</strong> {{ $csName }}<br>
        <strong>Metode</strong> {{ $paymentMethod }}
        @if(!empty($payReference) && $payReference !== '-')
          <br><strong>Ref. Bayar</strong> {{ $payReference }}
        @endif
      </p>

      <hr class="receipt__divider">

      <table class="receipt__items">
        <thead>
          <tr>
            <th>Item</th>
            <th class="text-end">Harga</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($baskets as $item)
          <tr>
            <td>
              <span class="receipt__item-name">{{ $item->name }}</span>
              <span class="receipt__item-detail">
                {{ $item->variant }} · {{ $item->size }}
                @if(!empty($item->ice) && $item->ice !== '-')
                  · {{ $item->ice }}
                @endif
                @if(!empty($item->sugar) && $item->sugar !== '-')
                  · {{ $item->sugar }}
                @endif
              </span>
            </td>
            <td class="text-end">
              {{ $item->quantity }}×<br>
              Rp{{ number_format($item->price, 0, ',', '.') }}<br>
              <strong>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</strong>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="2" class="text-center text-muted py-2">Tidak ada item pesanan.</td>
          </tr>
          @endforelse
        </tbody>
      </table>

      <hr class="receipt__divider">

      <table class="receipt__totals">
        <tr>
          <td class="label">Subtotal</td>
          <td class="value">Rp{{ number_format($total, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td class="label">Dibayar</td>
          <td class="value">Rp{{ number_format($pay, 0, ',', '.') }}</td>
        </tr>
        @if(($paymentMethod ?? '') !== 'QRIS')
        <tr>
          <td class="label">Kembalian</td>
          <td class="value">Rp{{ number_format($change, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="grand">
          <td>TOTAL</td>
          <td class="value">Rp{{ number_format($total, 0, ',', '.') }}</td>
        </tr>
      </table>

      <p class="receipt__thanks">Terima kasih — silakan datang kembali!</p>
    </article>

    <div class="receipt-actions no-print">
      <button type="button" class="btn btn-primary" onclick="window.print()">
        Cetak ulang
      </button>
      <a href="{{ url('/home') }}" class="btn btn-outline-secondary">Kembali ke POS</a>
    </div>
  </div>

  <script>
    window.addEventListener('load', function () {
      setTimeout(function () { window.print(); }, 400);
    });
  </script>
</body>
</html>
