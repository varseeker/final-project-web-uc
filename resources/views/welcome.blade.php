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

    <div id="main-content" class="container py-3 py-md-4 welcome-page">
        <div class="welcome-page__hero mb-3">
            <h2 class="fw-bold page-header welcome-page__title mb-1">Menu Warkop Kayu</h2>
            <p class="text-muted small mb-0">Minuman, makanan, dan paket bundle — tap filter untuk lihat per kategori.</p>
        </div>

        @include('partials.welcome-menu-catalog')
    </div>

    <div class="mt-4 border-top">
        @include('layouts.footer')
    </div>
</body>
</html>
