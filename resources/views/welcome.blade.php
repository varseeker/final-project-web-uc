<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Underground Cafeee</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('img/main-logo.png') }}">
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</head>
<body class="bg-light">

    <!-- <nav class="navbar px-4">
        <div class="navbar-brand text-white">
            <strong>Underground<br>Cafe</strong>
        </div>
        <a href="#" class="btn btn-secondary">Add Order</a>
    </nav> -->
    @include('layouts.navbar')
    <div class="container py-5">
        <h2 class="fw-bold border-bottom pb-2 mb-4">Our Menu</h2>

        @foreach ($menuItems as $category => $items)
            <h4 class="fw-bold mb-3">{{ $category }}</h4>
            <div class="row row-cols-1 gy-4 mb-5">
                @foreach ($items as $item)
                <div class="col">
                  <div class="card">
                      <div class="card-body d-flex gap-3 align-items-center">
                          {{-- Gambar --}}
                          <img src="{{ $item->img_url }}" alt="{{ $item->name }}" class="menu-img">

                          {{-- Konten --}}
                          <div class="flex-grow-1">
                              <h5 class="card-title">{{ $item->name }}</h5>
                              @if ($item->most_ordered)
                                <span class="badge bg-danger text-white">Most Ordered</span>
                              @endif
                              <p class="card-text small text-muted">{{ $item->description }}</p>
                          </div>

                          {{-- Harga --}}
                          <div class="text-end price mt-3 mt-md-0">
                              Rp.{{ number_format($item->price , 2, ',', '.') }}
                          </div>
                      </div>
                  </div>
                </div>
                @endforeach
            </div>
        @endforeach
    </div>
    <div style="margin-top: 1.5rem; border-top: outset;">
        @include('layouts.footer')
    </div>

    <!-- <footer class="py-4 text-center">
        <div class="d-flex justify-content-center footer-links mb-2">
            <a href="#">About us</a>
            <a href="#">Contact Us</a>
            <a href="#">Help</a>
        </div>
        <div class="copyright">© Underground Cafe</div>
    </footer> -->

    {{-- Bootstrap 5 JS via CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- {{-- Custom JS --}}
    <script src="{{ asset('js/app.js') }}"></script> -->
</body>
</html>
