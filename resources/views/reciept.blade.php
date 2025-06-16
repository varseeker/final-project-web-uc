<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    @media print {
    @page {
        size: 80mm;
        margin: 0;
        padding: 5mm 8mm 0 8mm;
    }

    body {
        /* width: 80mm; */
        width: 302px;
        margin: 0;
        padding: 0;
        font-size: 12px; /* Sesuaikan */
    }

    /* Optional: hilangkan elemen yang tidak perlu saat cetak */
    .no-print {
        display: none !important;
    }

    * {
        page-break-inside: avoid;
    }
}
  </style>
</head>
<body>

  <div class="receipt">
    <h3 class="text-center">Underground Cafe</h3>
    <h5 class="text-center">Jl. Bintaro Permai No. 27, <br>
Kel. Pesanggrahan, Kec. Pesanggrahan, <br>
Jakarta Selatan 12320, DKI Jakarta</h5>
    <hr>
    

    <p>Order Time <?php date_default_timezone_set("Asia/Jakarta"); echo date("Y/m/d - h:s:i"); ?> <br> No. Order: #123456 <br> Customer : {{$csName}} </p>
    <hr>
    
    
    
    @php $total = 0; @endphp
    @php $change = 0; @endphp
    @foreach ($baskets as $index => $rock)
    @php $total += $rock->quantity * $rock->price; @endphp

    Kopi Susu
    <table>
        <tr>
          
            <td>{{ $rock->quantity }} x</td>
            <td>Rp {{ number_format($rock->price, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Subtotal : Rp {{ number_format($rock->subtotal, 0, ',', '.') }}</td>
        </tr>
        <!-- dst -->
         
    </table>
    @endforeach
    
    
    <hr>
    
    <table>
        <tr>
          
            <td>Total</td>
            <td>Rp{{ number_format($total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Paid</td>

            <td>Rp{{ number_format($pay, 0, ',', '.') }}</td>
        </tr>
        <tr>
            @php $change = $pay - $total; @endphp
            <td>Change</td>
            <td>Rp{{ number_format($change, 0, ',', '.') }}</td>
        </tr>
        <!-- dst -->
    </table>
</div>

    <h4 class="text-center"> Thank you, come again!</h4>
    <script>
        window.onload = () => window.print();
    </script>

    <button type="button" 
            class="btn btn-outline-danger rounded-3 px-3 py-1 ms-auto no-print" 
            onclick="location.href='/home'">
            Back to POS
    </button>
</body>
</html>