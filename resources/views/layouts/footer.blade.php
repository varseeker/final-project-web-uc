@php
    $inventoryUrl = config('inventory.enabled') && config('inventory.base_url') ? config('inventory.base_url') : null;
@endphp

<footer class="footer py-4 shadow-sm" aria-label="Footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="h5 mb-1">Warkop Kayu</h2>
                <p class="small mb-2 opacity-75">"Hidden Places with Amazing Tastes."</p>
                <ul class="list-unstyled d-flex gap-3 mb-3 footer-links">
                    <li>
                        <a href="#" class="text-white text-decoration-none" aria-label="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-white text-decoration-none" aria-label="Twitter">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-white text-decoration-none" aria-label="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                    </li>
                </ul>
                @auth
                    <div class="footer-quick-links">
                        <a href="{{ route('home') }}">Menu POS</a>
                        <a href="{{ route('customers.index') }}">Member</a>
                        @if ($inventoryUrl)
                            <a href="{{ $inventoryUrl }}" target="_blank" rel="noopener noreferrer">Inventory</a>
                        @endif
                    </div>
                @endauth
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="copyright small opacity-75">&copy; Warkop Kayu {{ date('Y') }}</div>
            </div>
        </div>
    </div>
</footer>
