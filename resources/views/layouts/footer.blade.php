@php
    $inventoryUrl = config('inventory.enabled') && config('inventory.base_url') ? config('inventory.base_url') : null;
@endphp

<footer class="pos-footer pos-footer--minimal" aria-label="Footer">
    <div class="container">
        <div class="pos-footer__inner">
            <p class="pos-footer__copy mb-0">
                &copy; {{ date('Y') }} Warkop Kayu · Kasir POS
            </p>
            <div class="pos-footer__links">
                @auth
                    <a href="{{ route('home') }}" class="pos-footer__link">Menu</a>
                    <a href="{{ route('customers.index') }}" class="pos-footer__link">Member</a>
                @endauth
                @if ($inventoryUrl)
                    <a href="{{ $inventoryUrl }}" class="pos-footer__link" target="_blank" rel="noopener noreferrer">Inventory</a>
                @endif
                @guest
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="pos-footer__link">Login</a>
                    @endif
                @endguest
            </div>
        </div>
    </div>
</footer>
