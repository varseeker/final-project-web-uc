@php
    $inventoryUrl = config('inventory.enabled') && config('inventory.base_url') ? config('inventory.base_url') : null;
@endphp

<footer class="pos-footer" aria-label="Informasi situs">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-md-5 col-lg-4">
                <div class="pos-footer__brand">
                    <img src="{{ asset('img/main-logo.svg') }}" alt="" class="pos-footer__logo" width="36" height="36" decoding="async">
                    <div>
                        <p class="pos-footer__title mb-0">Warkop Kayu</p>
                        <p class="pos-footer__tagline mb-0">Rasa autentik, pelayanan cepat.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-4">
                <p class="pos-footer__heading">Navigasi</p>
                <ul class="list-unstyled pos-footer__links mb-0">
                    @auth
                        <li>
                            <a href="{{ route('home') }}" class="pos-footer__link">
                                <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
                                Menu Kasir
                            </a>
                        </li>
                    @endauth
                    @if ($inventoryUrl)
                        <li>
                            <a href="{{ $inventoryUrl }}" class="pos-footer__link" target="_blank" rel="noopener noreferrer">
                                <i class="bi bi-box-seam" aria-hidden="true"></i>
                                Inventory Management
                            </a>
                        </li>
                    @endif
                    @guest
                        @if (Route::has('login'))
                            <li>
                                <a href="{{ route('login') }}" class="pos-footer__link">
                                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                                    Login Kasir
                                </a>
                            </li>
                        @endif
                    @endguest
                </ul>
            </div>

            <div class="col-md-3 col-lg-4">
                <p class="pos-footer__heading">Sistem</p>
                <p class="pos-footer__meta mb-2">
                    <i class="bi bi-shop" aria-hidden="true"></i>
                    Point of Sale — Warkop Kayu
                </p>
                <p class="pos-footer__copyright mb-0">
                    &copy; {{ date('Y') }} Warkop Kayu. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </div>
</footer>
