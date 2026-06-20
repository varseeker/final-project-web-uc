@php
    $homeUrl = auth()->check() ? route('home') : url('/');
    $inventoryUrl = config('inventory.enabled') && config('inventory.base_url') ? config('inventory.base_url') : null;
@endphp

<header class="pos-site-header">
    <nav class="navbar navbar-expand-lg navbar-light pos-navbar pos-navbar--minimal" aria-label="Navigasi utama">
        <div class="container pos-navbar__container">
            <a href="{{ $homeUrl }}" class="pos-navbar__brand" aria-label="Beranda Warkop Kayu POS">
                <img src="{{ asset('img/main-logo.svg') }}" alt="" class="pos-navbar__logo" width="36" height="36" decoding="async">
                <span class="pos-navbar__title">Warkop Kayu</span>
            </a>

            @auth
                <span class="pos-navbar__user-badge d-none d-lg-inline-flex" title="{{ Auth::user()->name }}">
                    <i class="bi bi-person-fill" aria-hidden="true"></i>
                    <span>{{ Auth::user()->name }}</span>
                </span>
            @endauth

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

            <div class="collapse navbar-collapse pos-navbar__menu" id="mainNavbar">
                <ul class="navbar-nav ms-lg-auto">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="pos-navbar__link @if(request()->routeIs('login')) is-active @endif" href="{{ route('login') }}">
                                    Login
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item d-lg-none">
                            <span class="pos-navbar__user-badge pos-navbar__user-badge--mobile">
                                <i class="bi bi-person-fill" aria-hidden="true"></i>
                                {{ Auth::user()->name }}
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="pos-navbar__link @if(request()->routeIs('home')) is-active @endif" href="{{ route('home') }}">
                                Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="pos-navbar__link @if(request()->routeIs('customers.*')) is-active @endif" href="{{ route('customers.index') }}">
                                Member
                            </a>
                        </li>
                        @if ($inventoryUrl)
                            <li class="nav-item">
                                <a class="pos-navbar__link" href="{{ $inventoryUrl }}" target="_blank" rel="noopener noreferrer">
                                    Inventory
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="pos-navbar__link pos-navbar__link--muted" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Keluar
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</header>
