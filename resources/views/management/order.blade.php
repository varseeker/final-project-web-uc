@extends('layouts.app')

@section('content')
<!-- Main Content -->
<main class="container py-4">
@include('layouts.goBack')

<a href="/dashboard/order/export/" class="text-white align-middle">
            <button  class="btn btn-outline-secondary">
                Export To XLSX
            </button>
            
    </a>

    <div class="my-3 me-5 d-flex justify-content-start">
      <h1>Order Management</h1>
    </div>
    <div class="my-3 me-5 d-flex justify-content-end">
      
    </div>

    <table class="table table-striped">
  <thead class="table-dark">
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Total Transaction</th>
      <th scope="col">Amount Payed</th>
      <th scope="col">Change</th>
      <th scope="col">Customer Name</th>
      <th scope="col">Status Transaction</th>
      <th scope="col">Status Payment</th>
      <th scope="col">Cashier</th>
      <th scope="col">Payment Reference</th>
      <th scope="col">Manage</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($cashiers as $cashier)
    <tr>
      <th scope="row" class="align-middle">{{ $cashier->id }}</th>
      <td class="align-middle">{{ $cashier->total }}</td>
      <td class="align-middle">{{ $cashier->amountPaid }}</td>
      <td class="align-middle">{{ $cashier->amountChange }}</td>
      <td class="align-middle">{{ $cashier->customer }}</td>
      <td class="align-middle">{{ $cashier->status }}</td>
      <td class="align-middle">{{ $cashier->{'payment-status'} }}</td>
      <td class="align-middle">{{ $cashier->name }}</td>
      <td class="align-middle">{{ $cashier->payReference }}</td>
      <td class="align-middle">
        <a href="/dashboard/order/{{ $cashier->id }}" class="text-white align-middle">
            <button  class="btn btn-outline-secondary">
                void
            </button>
        </a>
        <a href="/dashboard/order/{{ $cashier->id }}" class="text-white align-middle">
            <button  class="btn btn-outline-primary">
                Ordered Items
            </button>
        </a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

</main>
@endsection