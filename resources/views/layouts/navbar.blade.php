@php
    $homeUrl = auth()->check() ? route('home') : url('/');
    $inventoryUrl = config('inventory.enabled') && config('inventory.base_url') ? config('inventory.base_url') : null;
@endphp

<header class="pos-site-header">
    <nav class="navbar navbar-expand-lg navbar-dark pos-navbar" aria-label="Navigasi utama">
        <div class="container">
            <a href="{{ $homeUrl }}" class="navbar-brand pos-navbar__brand" aria-label="Beranda Warkop Kayu POS">
                <img src="{{ asset('img/main-logo.svg') }}" alt="" class="pos-navbar__logo" width="42" height="42" decoding="async">
                <span class="pos-navbar__brand-text">
                    <span class="pos-navbar__title">Warkop Kayu</span>
                    <span class="pos-navbar__subtitle">Kasir POS</span>
                </span>
            </a>

            <button
                class="navbar-toggler pos-navbar__toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
                aria-controls="mainNavbar"
                aria-expanded="false"
                aria-label="Buka menu"
                id="mainNavbarToggler"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse pos-navbar__collapse" id="mainNavbar">
                <ul class="navbar-nav ms-lg-auto align-items-lg-center">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link pos-nav-link @if(request()->routeIs('login')) is-active @endif" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                                    <span>Login Kasir</span>
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item d-lg-none">
                            <div class="pos-navbar__user pos-navbar__user--mobile">
                                <i class="bi bi-person-circle" aria-hidden="true"></i>
                                <div>
                                    <span class="pos-navbar__user-name">{{ Auth::user()->name }}</span>
                                    <span class="pos-navbar__user-role">Staf kasir</span>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link pos-nav-link @if(request()->routeIs('home')) is-active @endif" href="{{ route('home') }}">
                                <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
                                <span>Menu Kasir</span>
                            </a>
                        </li>

                        @if ($inventoryUrl)
                            <li class="nav-item">
                                <a class="nav-link pos-nav-link" href="{{ $inventoryUrl }}" target="_blank" rel="noopener noreferrer">
                                    <i class="bi bi-box-seam" aria-hidden="true"></i>
                                    <span>Inventory</span>
                                    <i class="bi bi-box-arrow-up-right pos-nav-link__external" aria-hidden="true"></i>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item d-none d-lg-flex">
                            <div class="pos-navbar__user" aria-label="Pengguna aktif">
                                <i class="bi bi-person-circle" aria-hidden="true"></i>
                                <div>
                                    <span class="pos-navbar__user-name">{{ Auth::user()->name }}</span>
                                    <span class="pos-navbar__user-role">Staf kasir</span>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link pos-nav-link pos-nav-link--logout" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                                <span>Keluar</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</header>
