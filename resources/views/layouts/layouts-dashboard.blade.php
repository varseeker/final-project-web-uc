<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sharia Empower</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/main-logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function(){
            $('#toggle-sidebar').click(function(){
                $('#sidebar').toggleClass('closed');
                $('.main-content').toggleClass('shifted full-widt');
                $('.navbar').toggleClass('shifted full-width');
                $('.footer').toggleClass('shifted full-width');
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const dropdownToggle = document.getElementById('navbarDropdown');
            const dropdownMenu = document.getElementById('dropdownMenu');

            // Toggle dropdown visibility
            dropdownToggle.addEventListener('click', function (e) {
                e.preventDefault();
                dropdownMenu.classList.toggle('show');
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        });
    </script>
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            background-color: #222;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1500;
        }
        .sidebar.closed {
            transform: translateX(-100%);
        }
        .sidebar-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-list li {
            margin-bottom: 10px;
        }
        .sidebar-list a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #fff;
            transition: background-color 0.3s;
        }
        .sidebar-list a:hover {
            background-color: #333;
        }
        .toggle-btn {
            /* position: fixed; */
            /* top: 20px; */
            left: 270px;
            z-index: 1100;
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            transition: left 0.3s ease;
        }
        .toggle-btn.closed {
            left: 20px;
        }
        .navbar, .footer, .main-content {
            transition: margin-left 0.3s ease;
        }
        .navbar.full-width, .footer.full-width, .main-content.full-width {
            margin-left: 0;
        }
        .navbar.shifted, .footer.shifted, .main-content.shifted {
            margin-left: 250px; /* Adjust based on sidebar width */
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.closed {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0; /* Adjust based on sidebar width */
            }
            .navbar.shifted, .footer.shifted, .main-content.shifted {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm shifted">
        <div class="container">
            <button class="toggle-btn" id="toggle-sidebar">☰</button>
            <a href="{{ url('/') }}" class="navbar-brand ms-2">
                <img src="{{ asset('img/logo-with-name-black.png') }}" alt="Web Banking Hero Image" class="img-fluid" style="height: 40px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button">
                            {{ Auth::user()->name }}
                        </a>
                        <div id="dropdownMenu" class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="/">
                                {{ __('Home') }}
                            </a>
                            @if(Auth::user()->role !== 'admin')
                            <a class="dropdown-item" href="{{ route('profile', Auth::user()->id_nasabah) }}">
                                {{ __('Profile') }}
                            </a>
                            @endif
                            <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="sidebar" id="sidebar">
        <ul class="sidebar-list">
        @if(Auth::user()->role === 'admin')
            <li><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="{{ route('nasabah.index') }}"><i class="fas fa-user"></i> Nasabah</a></li>
            <li><a href="{{ route('rekening.index') }}"><i class="fas fa-book"></i> Rekening</a></li>
            <li><a href="{{ route('transaksi.index') }}"><i class="fas fa-book"></i> Transaksi</a></li>
            <li><a href="{{ route('produk.index') }}"><i class="fas fa-book"></i> Produk</a></li>
        @endif
        @if(Auth::user()->role === 'nasabah')
            <li><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
        @endif
        </ul>
    </div>
    <div class="main-content shifted" id="main-content">
        <div class="container">    
            <div class="row" style="margin-top: 1em; margin-bottom: 3em;">
                @yield('content')
            </div>
        </div>
    </div>
    <footer class="footer py-3 bg-white shadow-sm shifted" style="border-top: outset"> 
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>Bank Sharia Empower</h2>
                    <p>"Empowering Together with sharia."</p>
                    <ul class="list-unstyled d-flex text-muted"> 
                        <li class="ms-3">
                            <a href="#" class="text-muted text-decoration-none">
                              <i class="fab fa-facebook-f fa-lg"></i>
                            </a>
                        </li>
                        <li class="ms-3">
                            <a href="#" class="text-muted text-decoration-none">
                              <i class="fab fa-twitter fa-lg"></i>
                            </a>
                        </li>
                        <li class="ms-3">
                            <a href="#" class="text-muted text-decoration-none">
                              <i class="fab fa-instagram fa-lg"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted">© 2024 Bank Sharia Empower. All Rights Reserved.</p>
                    <a href="#" class="text-muted text-decoration-none">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
