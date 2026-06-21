(function () {
    'use strict';

    var lookupUrl = document.body.dataset.memberLookupUrl || '/customers/lookup';
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function qs(selector, root) {
        return (root || document).querySelector(selector);
    }

    function qsa(selector, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(selector));
    }

    function showToast(message) {
        if (typeof window.showToast === 'function') {
            window.showToast(message);
            return;
        }

        alert(message);
    }

    function setMemberPanel(mode) {
        var existingPanel = qs('[data-member-panel="existing"]');
        var newPanel = qs('[data-member-panel="new"]');
        var customerNameInput = qs('#customerName');
        var existingPhone = qs('#memberPhoneExisting');
        var newPhone = qs('#memberPhoneNew');

        if (existingPanel) {
            existingPanel.hidden = mode !== 'existing';
        }

        if (newPanel) {
            newPanel.hidden = mode !== 'new';
        }

        if (existingPhone) {
            existingPhone.disabled = mode !== 'existing';
        }

        if (newPhone) {
            newPhone.disabled = mode !== 'new';
        }

        if (customerNameInput) {
            customerNameInput.readOnly = mode === 'existing';
        }
    }

    function renderMemberResult(customer) {
        var resultEl = qs('[data-member-result]');
        var customerIdInput = qs('#customerId');

        if (!resultEl) {
            return;
        }

        if (!customer) {
            resultEl.hidden = true;
            resultEl.innerHTML = '';
            if (customerIdInput) {
                customerIdInput.value = '';
            }
            return;
        }

        if (customerIdInput) {
            customerIdInput.value = String(customer.id);
        }

        var nameInput = qs('#customerName');
        if (nameInput) {
            nameInput.value = customer.name;
        }

        resultEl.hidden = false;
        var discountText = '';
        if (Number(customer.loyalty_discount_percent || 0) > 0) {
            discountText =
                ' <span class="badge rounded-pill text-bg-success ms-1">' +
                'Diskon ' + customer.loyalty_discount_percent + '%</span>';
        }

        resultEl.innerHTML =
            '<div class="pos-member-result">' +
            '<strong>' + customer.name + '</strong>' +
            '<span class="text-muted"> · ' + customer.phone + '</span>' +
            '<span class="badge rounded-pill text-bg-primary ms-1">' +
            Number(customer.loyalty_points || 0).toLocaleString('id-ID') + ' poin</span>' +
            discountText +
            '</div>';
    }

    function lookupMember(phone, onSuccess, onError) {
        if (!phone) {
            onError('Masukkan nomor telepon member.');
            return;
        }

        if (window.PosLoading) {
            window.PosLoading.start('Mencari member...');
        }

        fetch(lookupUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ phone: phone }),
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (window.PosLoading) {
                    window.PosLoading.done();
                }

                if (result.data.found) {
                    onSuccess(result.data.customer);
                    return;
                }

                onError(result.data.message || 'Member tidak ditemukan.');
            })
            .catch(function () {
                if (window.PosLoading) {
                    window.PosLoading.done();
                }
                onError('Gagal mencari member. Coba lagi.');
            });
    }

    function initMemberCheckout() {
        var form = qs('.pos-checkout-form');
        if (!form) {
            return;
        }

        var modeInputs = qsa('input[name="memberMode"]', form);
        var lookupBtn = qs('[data-member-lookup]', form);
        var resultEl = qs('[data-member-result]', form);

        function activePhoneInput(mode) {
            if (mode === 'new') {
                return qs('#memberPhoneNew', form);
            }

            return qs('#memberPhoneExisting', form);
        }

        modeInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                setMemberPanel(input.value);
                renderMemberResult(null);

                if (resultEl) {
                    resultEl.classList.remove('is-invalid');
                }
            });
        });

        var checked = modeInputs.find(function (input) {
            return input.checked;
        });
        setMemberPanel(checked ? checked.value : 'none');

        if (lookupBtn) {
            lookupBtn.addEventListener('click', function () {
                var memberPhoneInput = activePhoneInput('existing');
                lookupMember(
                    memberPhoneInput ? memberPhoneInput.value.trim() : '',
                    function (customer) {
                        renderMemberResult(customer);
                        if (resultEl) {
                            resultEl.classList.remove('is-invalid');
                        }
                    },
                    function (message) {
                        renderMemberResult(null);
                        showToast(message);
                        if (resultEl) {
                            resultEl.hidden = false;
                            resultEl.classList.add('is-invalid');
                            resultEl.innerHTML = '<span class="text-danger small">' + message + '</span>';
                        }
                    }
                );
            });

            var existingPhoneInput = qs('#memberPhoneExisting', form);
            if (existingPhoneInput) {
                existingPhoneInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        lookupBtn.click();
                    }
                });
            }
        }

        form.addEventListener('submit', function (event) {
            var modeInput = modeInputs.find(function (input) {
                return input.checked;
            });
            var mode = modeInput ? modeInput.value : 'none';
            var customerName = qs('#customerName', form);

            if (!customerName || !customerName.value.trim()) {
                event.preventDefault();
                customerName?.classList.add('is-invalid');
                customerName?.focus();
                showToast('Nama pelanggan wajib diisi.');
                return;
            }

            if (mode === 'existing') {
                var customerId = qs('#customerId', form);
                var existingPhoneInput = qs('#memberPhoneExisting', form);
                if (!customerId || !customerId.value) {
                    event.preventDefault();
                    showToast('Cari member dengan nomor telepon terlebih dahulu.');
                    if (resultEl) {
                        resultEl.classList.add('is-invalid');
                    }
                    existingPhoneInput?.focus();
                    return;
                }
            }

            if (mode === 'new') {
                var newPhoneInput = qs('#memberPhoneNew', form);
                if (!newPhoneInput || !newPhoneInput.value.trim()) {
                    event.preventDefault();
                    newPhoneInput?.classList.add('is-invalid');
                    newPhoneInput?.focus();
                    showToast('Nomor telepon wajib diisi untuk member baru.');
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMemberCheckout);
    } else {
        initMemberCheckout();
    }
})();
