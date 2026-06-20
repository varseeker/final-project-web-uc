<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#503b31">

  <title>Warkop Kayu</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('img/main-logo.svg') }}">
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
  @include('layouts.partials.assets')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-light pos-app">
    @include('layouts.partials.loading')
    <a href="#main-content" class="skip-link">Lewati ke konten</a>
    @include('layouts.navbar')

    <main id="main-content" class="pos-main container py-3 py-md-4 welcome-page">
        <div class="welcome-page__hero mb-3">
            <h2 class="fw-bold page-header welcome-page__title mb-1">Menu Warkop Kayu</h2>
            <p class="text-muted small mb-0">Minuman, makanan, dan paket bundle — tap filter untuk lihat per kategori.</p>
        </div>

        @include('partials.welcome-menu-catalog')
    </main>

    <div class="pos-footer-wrap">
        @include('layouts.footer')
    </div>
</body>
</html>
