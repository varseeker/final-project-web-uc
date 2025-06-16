@extends('layouts.app')

@section('content')
<!-- Main Content -->
<main class="container py-4">
@include('layouts.goBack')

    <h1 class="mt-3">Order Details</h1>
    
    <div class="row justify-content-center">
        <div class="col-md-12">
        <div class="" style="max-width: 1200px;">
            <div class="row g-0">
                    <!-- <div class="" style="padding: 20px 30px;"> -->

                      <table class="table table-striped border border-2 rounded-2 bordered-dark">
                        <thead class="table-dark">
                          <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Order ID</th>
                            <th scope="col">Cashier</th>
                            <th scope="col">Item Quantity</th>
                            <th scope="col">Variant</th>
                            <th scope="col">Size</th>
                            <th scope="col">Ice</th>
                            <th scope="col">Sugar</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($cashierItems as $cashierItem)
                          <tr>
                            <th scope="row" class="align-middle">{{ $cashierItem->id }}</th>
                            <td class="align-middle">{{ $cashierItem->order_id }}</td>
                            <td class="align-middle">{{ $cashierItem->name }}</td>
                            <td class="align-middle">{{ $cashierItem->quantity }}</td>
                            <td class="align-middle">{{ $cashierItem->variant }}</td>
                            <td class="align-middle">{{ $cashierItem->size }}</td>
                            <td class="align-middle">{{ $cashierItem->ice }}</td>
                            <td class="align-middle">{{ $cashierItem->sugar }}</td>
                            <td class="align-middle">{{ $cashierItem->subtotal }}</td>
                            <td class="align-middle">{{ $cashierItem->status }}</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
    
                    <!-- </div> -->
            </div>
        </div>
</div>
</div>

</main>
@endsection