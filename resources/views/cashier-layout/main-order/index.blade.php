@extends('layouts.layouts-main_menu')

@section('content')
<!-- Main Content -->
<main class="container py-4">

  <!-- Top Action Bar -->
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-primary">Submit Order</button>
      <span class="fw-bold">Add Order +</span>
    </div>
    <div class="d-flex align-items-center gap-4 me-4">
      <label class="fw-bold mb-0">Table:</label>
      <input type="text" class="form-control form-control" style="width: 8rem;">
      <button class="btn btn-primary">Cart <i class="bi bi-cart"></i></button>      
    </div>
  </div>
    <hr>
  <!-- Menu Cards -->
  @foreach ($groupedItems as $category => $items)
    <h5 class="fw-bold mb-3">{{ $category }}</h5>
    <div class="row row-cols-md-4 g-3  mb-5">
      @foreach ($items as $item)
        <div class="col">
          <div class="card text-center shadow-sm" style="width: 18rem;">
            <img src="{{ $item['image'] }}" class="img-box bg-light d-flex justify-content-center align-items-center rounded-image-menu" style="height: 200px;" alt="{{ $item['name'] }}">
            <div class="card-body d-flex flex-column justify-content-between">
              <div>
                <h6 class="fw-bold">{{ $item['name'] }}</h6>
                <p class="text-muted mb-2">Rp.{{ number_format($item['price'], 0, ',', '.') }},00</p>
              </div>
              <button class="btn btn-secondary mt-auto">
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
@endsection