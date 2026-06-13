<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#503b31">

  <title>Warkop Kayu</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('img/main-logo.png') }}">
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  @include('layouts.partials.assets')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-light">
    <a href="#main-content" class="skip-link">Lewati ke konten</a>
    @include('layouts.navbar')

    <div id="main-content" class="container py-5">
        <h2 class="fw-bold page-header">Our Menu</h2>
        <p class="text-muted mb-4">Jelajahi menu favorit kami.</p>

        @forelse ($menuItems as $category => $items)
            <h4 class="fw-bold mb-3 category-heading" id="cat-{{ Str::slug($category) }}">{{ $category }}</h4>
            <div class="row row-cols-1 gy-3 mb-5">
                @foreach ($items as $item)
                <div class="col">
                  <div class="card menu-card h-100">
                      <div class="card-body d-flex flex-column flex-md-row gap-3 align-items-md-center">
                          <img src="{{ asset($item->img_url) }}" alt="{{ $item->name }}" class="menu-img flex-shrink-0" loading="lazy" width="80" height="80">

                          <div class="flex-grow-1">
                              <h5 class="card-title mb-1">{{ $item->name }}</h5>
                              @if ($item->most_ordered)
                                <span class="badge bg-danger text-white">Most Ordered</span>
                              @endif
                              <p class="card-text small text-muted mb-0 mt-1">{{ $item->description }}</p>
                          </div>

                          <div class="text-md-end price flex-shrink-0">
                              Rp{{ number_format($item->price, 0, ',', '.') }}
                          </div>
                      </div>
                  </div>
                </div>
                @endforeach
            </div>
        @empty
            <div class="empty-state">
                <i class="bi bi-cup-hot"></i>
                <p class="fw-semibold mb-0">Menu belum tersedia</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4 border-top">
        @include('layouts.footer')
    </div>
</body>
</html>
