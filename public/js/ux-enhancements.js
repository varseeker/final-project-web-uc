(function () {
    'use strict';

    function getCartCount() {
        var raw = document.body.dataset.cartCount;
        if (raw === undefined || raw === '') {
            raw = document.documentElement.dataset.cartCount;
        }
        return parseInt(raw || '0', 10) || 0;
    }

    function initCartBadge() {
        var n = getCartCount();
        document.querySelectorAll('[data-cart-badge]').forEach(function (el) {
            el.textContent = n;
            el.classList.toggle('d-none', n === 0);
        });
    }

    function initFormLoading() {
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function () {
                var btn = form.querySelector('[type="submit"]');
                if (!btn || btn.classList.contains('no-loading')) return;
                btn.classList.add('is-loading');
                btn.setAttribute('aria-busy', 'true');
            });
        });
    }

    function initCustomerNameValidation() {
        var input = document.querySelector('input[name="customerName"]');
        var form = input && input.closest('form');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            if (input.value.trim()) return;
            e.preventDefault();
            input.classList.add('is-invalid');
            input.focus();
            if (typeof showToast === 'function') {
                showToast('Nama pelanggan wajib diisi.');
            }
        });

        input.addEventListener('input', function () {
            input.classList.remove('is-invalid');
        });
    }

    function initMenuCards() {
        document.querySelectorAll('.pos-menu-card[data-bs-toggle="modal"]').forEach(function (trigger) {
            trigger.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    trigger.click();
                }
            });
        });
    }

    function initPosDock() {
        var count = getCartCount();
        var hasItems = count > 0 || document.querySelector('.pos-cart-item') !== null;
        document.querySelectorAll('.pos-dock [data-bs-target="#orderModal"]').forEach(function (btn) {
            btn.disabled = !hasItems;
        });
    }

    function initOrderModalFromCart() {
        document.querySelectorAll('[data-open-order-modal]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var cartEl = document.getElementById('cartModal');
                var orderEl = document.getElementById('orderModal');
                if (!cartEl || !orderEl) return;
                var cartModal = bootstrap.Modal.getInstance(cartEl) || new bootstrap.Modal(cartEl);
                cartModal.hide();
                cartEl.addEventListener('hidden.bs.modal', function openOrder() {
                    cartEl.removeEventListener('hidden.bs.modal', openOrder);
                    bootstrap.Modal.getOrCreateInstance(orderEl).show();
                });
            });
        });
    }

    function initNavbarCollapse() {
        var collapseEl = document.getElementById('mainNavbar');
        var toggler = document.getElementById('mainNavbarToggler');
        if (!collapseEl || !toggler || typeof bootstrap === 'undefined') return;

        collapseEl.addEventListener('shown.bs.collapse', function () {
            toggler.classList.remove('collapsed');
            toggler.setAttribute('aria-expanded', 'true');
            toggler.setAttribute('aria-label', 'Tutup menu');
        });

        collapseEl.addEventListener('hidden.bs.collapse', function () {
            toggler.classList.add('collapsed');
            toggler.setAttribute('aria-expanded', 'false');
            toggler.setAttribute('aria-label', 'Buka menu');
        });

        collapseEl.querySelectorAll('.nav-link[href]').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth >= 992) return;
                var instance = bootstrap.Collapse.getInstance(collapseEl);
                if (instance && collapseEl.classList.contains('show')) {
                    instance.hide();
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initCartBadge();
        initPosDock();
        initOrderModalFromCart();
        initFormLoading();
        initCustomerNameValidation();
        initMenuCards();
        initNavbarCollapse();
    });
})();
