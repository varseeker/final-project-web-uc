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
      <td class="align-middle">{{ $payment->totalPay }}</td>
      <td class="align-middle">{{ $payment->method }}</td>
      <td class="align-middle">{{ $payment->status }}</td>
      <td class="align-middle">{{ $payment->reference }}</td>
      <td class="align-middle">
        <a href="/dashboard/payment/{{ $payment->id }}" class="text-white align-middle">
            <button  class="btn btn-outline-secondary">
                void
            </button>
        </a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

</main>
@endsection