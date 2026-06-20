(function () {
    'use strict';

    var activeRequests = 0;
    var root;
    var bar;
    var label;
    var hideTimer;

    function getRoot() {
        if (!root) {
            root = document.getElementById('pos-loading');
            bar = root ? root.querySelector('.pos-loading__bar') : null;
            label = root ? root.querySelector('.pos-loading__label') : null;
        }

        return root;
    }

    function setMessage(message) {
        if (label && message) {
            label.textContent = message;
        }
    }

    function start(message) {
        var el = getRoot();

        if (!el || !bar) {
            return;
        }

        window.clearTimeout(hideTimer);
        activeRequests += 1;

        setMessage(message || 'Memproses...');
        el.hidden = false;
        el.setAttribute('aria-hidden', 'false');
        document.body.classList.add('pos-is-loading');
        bar.classList.remove('is-complete');
        bar.classList.add('is-active');
    }

    function done() {
        var el = getRoot();

        if (!el || !bar) {
            return;
        }

        activeRequests = Math.max(0, activeRequests - 1);

        if (activeRequests > 0) {
            return;
        }

        bar.classList.add('is-complete');
        bar.classList.remove('is-active');

        hideTimer = window.setTimeout(function () {
            el.hidden = true;
            el.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('pos-is-loading');
            bar.classList.remove('is-complete');
        }, 350);
    }

    function shouldSkipForm(form) {
        if (!form || form.tagName !== 'FORM') {
            return true;
        }

        if (form.classList.contains('no-loading')) {
            return true;
        }

        if (form.getAttribute('target') === '_blank') {
            return true;
        }

        return false;
    }

    function shouldSkipLink(anchor) {
        if (!anchor || !anchor.getAttribute('href')) {
            return true;
        }

        if (anchor.classList.contains('no-loading')) {
            return true;
        }

        if (anchor.target === '_blank' || anchor.hasAttribute('download')) {
            return true;
        }

        var href = anchor.getAttribute('href');

        if (!href || href.charAt(0) === '#') {
            return true;
        }

        if (href.indexOf('javascript:') === 0) {
            return true;
        }

        try {
            var url = new URL(anchor.href, window.location.origin);

            if (url.origin !== window.location.origin) {
                return true;
            }
        } catch (error) {
            return true;
        }

        return false;
    }

    function bindEvents() {
        document.addEventListener('submit', function (event) {
            if (shouldSkipForm(event.target)) {
                return;
            }

            if (event.defaultPrevented) {
                return;
            }

            start(event.target.dataset.loadingMessage || 'Memproses...');
        }, true);

        document.addEventListener('click', function (event) {
            var anchor = event.target.closest('a[href]');

            if (shouldSkipLink(anchor)) {
                return;
            }

            if (event.defaultPrevented) {
                return;
            }

            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            start('Memuat halaman...');
        }, true);

        window.addEventListener('beforeunload', function () {
            if (activeRequests === 0) {
                start('Memuat halaman...');
            }
        });

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                done();
            }
        });

        if (window.fetch) {
            var nativeFetch = window.fetch;

            window.fetch = function () {
                start('Memproses...');

                return nativeFetch.apply(this, arguments).finally(function () {
                    done();
                });
            };
        }

        if (window.jQuery) {
            window.jQuery(document).ajaxStart(function () {
                start('Memproses...');
            });

            window.jQuery(document).ajaxStop(function () {
                done();
            });
        }
    }

    window.PosLoading = {
        start: start,
        done: done,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindEvents);
    } else {
        bindEvents();
    }
})();
