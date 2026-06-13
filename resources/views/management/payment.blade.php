@extends('layouts.app')

@section('content')
<!-- Main Content -->
<main class="container py-4">
@include('layouts.goBack')

    <a href="/dashboard/payment/export/" class="text-white align-middle">
            <button  class="btn btn-outline-secondary">
                Export To XLSX
            </button>
    </a>

    <div class="my-3 me-5 d-flex justify-content-start">
      <h1>Payment Record</h1>
    </div>

    <div class="my-3 me-5 d-flex justify-content-end">
      
    </div>

    <table class="table table-striped">
  <thead class="table-dark">
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Order ID</th>
      <th scope="col">Amount Payed</th>
      <th scope="col">Payment Method</th>
      <th scope="col">Status</th>
      <th scope="col">Transaction Reference</th>
      <th scope="col">Manage</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($payments as $payment)
    <tr>
      <th scope="row" class="align-middle">{{ $payment->id }}</th>
      <td class="align-middle">{{ $payment->order_id }}</td>
      <td class="align-middle">Rp{{ number_format($payment->totalPay, 0, ',', '.') }}</td>
      <td class="align-middle">{{ $payment->method }}</td>
      <td class="align-middle">{{ $payment->status }}</td>
      <td class="align-middle">
        @php
          $trxRef = ($payment->reference && $payment->reference !== '-') ? $payment->reference : ($payment->payReference ?? '-');
        @endphp
        <code class="small">{{ $trxRef }}</code>
      </td>
      <td class="align-middle">
        <a href="{{ url('/dashboard/payment/'.$payment->id) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-eye me-1"></i>Detail
        </a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

</main>
@endsection