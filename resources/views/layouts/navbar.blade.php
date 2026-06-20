@php
    $homeUrl = auth()->check() ? route('home') : url('/');
    $inventoryUrl = config('inventory.enabled') && config('inventory.base_url') ? config('inventory.base_url') : null;
@endphp

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" aria-label="Navigasi utama">
    <div class="container">
        <a href="{{ $homeUrl }}" class="navbar-brand text-white" aria-label="Beranda Warkop Kayu">
            <img src="{{ asset('img/main-logo.svg') }}" alt="" width="36" height="36" decoding="async">
            <strong>Warkop<br>Kayu</strong>
        </a>

        <button
            class="navbar-toggler collapsed border border-light"
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

        <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
            <ul class="navbar-nav align-items-lg-center gap-lg-1">
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link text-white @if(request()->routeIs('login')) active @endif" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Login') }}
                            </a>
                        </li>
                    @endif
                @else
                    <li class="nav-item">
                        <span class="navbar-text text-white-50 small d-none d-lg-inline">Login as</span>
                        <span class="nav-link text-white fw-bold py-lg-1 d-inline-block">{{ Auth::user()->name }}</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white @if(request()->routeIs('home')) active @endif" href="{{ route('home') }}">
                            <i class="bi bi-cash-stack me-1"></i>POS
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white @if(request()->routeIs('customers.*')) active @endif" href="{{ route('customers.index') }}">
                            <i class="bi bi-people me-1"></i>Member
                        </a>
                    </li>
                    @if ($inventoryUrl)
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ $inventoryUrl }}" target="_blank" rel="noopener noreferrer">
                                <i class="bi bi-box-seam me-1"></i>Inventory
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-1"></i>{{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
