(function () {
    'use strict';

    var lookupUrl = document.body.dataset.memberLookupUrl || '/customers/lookup';
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    var activeMember = null;
    var loyaltyChoice = null;

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

    function formatRupiah(value) {
        return 'Rp' + Number(value || 0).toLocaleString('id-ID');
    }

    function getOrderModal() {
        return qs('#orderModal');
    }

    function getCheckoutSummary() {
        return qs('[data-checkout-summary]', getOrderModal());
    }

    function getBaseSubtotal() {
        var summary = getCheckoutSummary();
        if (!summary) {
            return 0;
        }

        return parseInt(summary.getAttribute('data-checkout-subtotal') || '0', 10) || 0;
    }

    function calculateDiscountAmount(subtotal, discountPercent) {
        if (discountPercent <= 0 || subtotal <= 0) {
            return 0;
        }

        return Math.floor(subtotal * discountPercent / 100);
    }

    function updateCheckoutSummary(usePoints, discountPercent) {
        var summary = getCheckoutSummary();
        if (!summary) {
            return;
        }

        var subtotal = getBaseSubtotal();
        var discountAmount = usePoints ? calculateDiscountAmount(subtotal, discountPercent) : 0;
        var grandTotal = Math.max(0, subtotal - discountAmount);

        var subtotalEl = qs('[data-summary-subtotal]', summary);
        var discountRow = qs('[data-summary-discount-row]', summary);
        var discountPercentEl = qs('[data-summary-discount-percent]', summary);
        var discountAmountEl = qs('[data-summary-discount-amount]', summary);
        var grandTotalEl = qs('[data-summary-grand-total]', summary);

        if (subtotalEl) {
            subtotalEl.textContent = formatRupiah(subtotal);
        }

        if (discountRow) {
            discountRow.hidden = !usePoints || discountAmount <= 0;
        }

        if (discountPercentEl) {
            discountPercentEl.textContent = String(discountPercent);
        }

        if (discountAmountEl) {
            discountAmountEl.textContent = '- ' + formatRupiah(discountAmount);
        }

        if (grandTotalEl) {
            grandTotalEl.textContent = formatRupiah(grandTotal);
        }
    }

    function resetCheckoutSummary() {
        updateCheckoutSummary(false, 0);
    }

    function getSubmitButton(form) {
        return qs('[data-checkout-submit]', form) || qs('#checkoutSubmitBtn', form);
    }

    function getSelectedMemberMode(form) {
        var modeInput = qsa('input[name="memberMode"]', form).find(function (input) {
            return input.checked;
        });

        return modeInput ? modeInput.value : 'none';
    }

    function canProceedCheckout(form) {
        var customerName = qs('#customerName', form);
        if (!customerName || !customerName.value.trim()) {
            return false;
        }

        var mode = getSelectedMemberMode(form);

        if (mode === 'none') {
            return true;
        }

        if (mode === 'new') {
            var newPhoneInput = qs('#memberPhoneNew', form);
            return !!(newPhoneInput && newPhoneInput.value.trim());
        }

        if (mode === 'existing') {
            var customerId = qs('#customerId', form);
            if (!customerId || !customerId.value) {
                return false;
            }

            if (
                activeMember &&
                Number(activeMember.loyalty_discount_percent || 0) > 0 &&
                loyaltyChoice === null
            ) {
                return false;
            }

            return true;
        }

        return false;
    }

    function updateSubmitButtonState(form) {
        var submitBtn = getSubmitButton(form);
        if (!submitBtn) {
            return;
        }

        var allowed = canProceedCheckout(form);
        submitBtn.disabled = !allowed;
        submitBtn.setAttribute('aria-disabled', allowed ? 'false' : 'true');
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

    function getLoyaltyConfirmEl() {
        return qs('[data-loyalty-confirm]', getOrderModal());
    }

    function resetLoyaltyConfirm(form) {
        activeMember = null;
        loyaltyChoice = null;

        var useInput = qs('#useLoyaltyDiscount', form);
        if (useInput) {
            useInput.value = '';
        }

        var confirmEl = getLoyaltyConfirmEl();
        if (confirmEl) {
            confirmEl.hidden = true;
            confirmEl.innerHTML = '';
        }

        resetCheckoutSummary();
        updateSubmitButtonState(form);
    }

    function setLoyaltyChoice(form, usePoints) {
        loyaltyChoice = usePoints ? 'yes' : 'no';

        var discountPercent = Number(activeMember?.loyalty_discount_percent || 0);

        var useInput = qs('#useLoyaltyDiscount', form);
        if (useInput) {
            useInput.value = usePoints ? '1' : '0';
        }

        updateCheckoutSummary(usePoints, discountPercent);

        var confirmEl = getLoyaltyConfirmEl();
        var noteEl = confirmEl ? confirmEl.querySelector('[data-loyalty-choice-note]') : null;
        var yesBtn = confirmEl ? confirmEl.querySelector('[data-loyalty-choice="1"]') : null;
        var noBtn = confirmEl ? confirmEl.querySelector('[data-loyalty-choice="0"]') : null;

        if (noteEl) {
            noteEl.hidden = false;
            noteEl.textContent = usePoints
                ? 'Diskon ' + discountPercent + '% diterapkan. Total bayar sudah diperbarui.'
                : 'Poin tidak digunakan. Total bayar tetap subtotal penuh.';
        }

        if (yesBtn) {
            yesBtn.classList.toggle('active', usePoints);
            yesBtn.disabled = usePoints;
        }

        if (noBtn) {
            noBtn.classList.toggle('active', !usePoints);
            noBtn.disabled = !usePoints;
        }

        updateSubmitButtonState(form);
    }

    function renderLoyaltyConfirm(form, customer) {
        var confirmEl = getLoyaltyConfirmEl();
        var useInput = qs('#useLoyaltyDiscount', form);

        if (!confirmEl || !useInput) {
            return;
        }

        var discountPercent = Number(customer.loyalty_discount_percent || 0);

        if (discountPercent <= 0) {
            confirmEl.hidden = true;
            confirmEl.innerHTML = '';
            useInput.value = '0';
            loyaltyChoice = 'no';
            resetCheckoutSummary();
            updateSubmitButtonState(form);
            return;
        }

        activeMember = customer;
        loyaltyChoice = null;
        useInput.value = '';
        resetCheckoutSummary();

        confirmEl.hidden = false;
        confirmEl.innerHTML =
            '<p class="pos-loyalty-confirm__title mb-2 fw-semibold">' +
            '<i class="bi bi-stars text-warning me-1"></i>Konfirmasi penggunaan poin</p>' +
            '<p class="small text-muted mb-3">' +
            'Member memiliki ' + Number(customer.loyalty_points || 0).toLocaleString('id-ID') + ' poin ' +
            '(diskon ' + discountPercent + '% tersedia). Gunakan poin untuk transaksi ini?' +
            '</p>' +
            '<div class="d-flex flex-wrap gap-2 mb-2">' +
            '<button type="button" class="btn btn-success btn-sm" data-loyalty-choice="1">Ya, gunakan poin</button>' +
            '<button type="button" class="btn btn-outline-secondary btn-sm" data-loyalty-choice="0">Tidak</button>' +
            '</div>' +
            '<p class="small text-muted mb-0" data-loyalty-choice-note hidden></p>';

        confirmEl.querySelector('[data-loyalty-choice="1"]')?.addEventListener('click', function () {
            setLoyaltyChoice(form, true);
        });

        confirmEl.querySelector('[data-loyalty-choice="0"]')?.addEventListener('click', function () {
            setLoyaltyChoice(form, false);
        });

        updateSubmitButtonState(form);
        confirmEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function renderMemberResult(customer, form) {
        var resultEl = qs('[data-member-result]', form);
        var customerIdInput = qs('#customerId', form);

        if (!resultEl) {
            return;
        }

        if (!customer) {
            resultEl.hidden = true;
            resultEl.innerHTML = '';
            if (customerIdInput) {
                customerIdInput.value = '';
            }
            resetLoyaltyConfirm(form);
            return;
        }

        if (customerIdInput) {
            customerIdInput.value = String(customer.id);
        }

        var nameInput = qs('#customerName', form);
        if (nameInput) {
            nameInput.value = customer.name;
        }

        resultEl.hidden = false;
        resultEl.innerHTML =
            '<div class="pos-member-result">' +
            '<strong>' + customer.name + '</strong>' +
            '<span class="text-muted"> · ' + customer.phone + '</span>' +
            '<span class="badge rounded-pill text-bg-primary ms-1">' +
            Number(customer.loyalty_points || 0).toLocaleString('id-ID') + ' poin</span>' +
            '</div>';

        renderLoyaltyConfirm(form, customer);
        updateSubmitButtonState(form);
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
        var orderModal = getOrderModal();
        var submitBtn = getSubmitButton(form);

        function activePhoneInput(mode) {
            if (mode === 'new') {
                return qs('#memberPhoneNew', form);
            }

            return qs('#memberPhoneExisting', form);
        }

        modeInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                setMemberPanel(input.value);
                renderMemberResult(null, form);

                if (resultEl) {
                    resultEl.classList.remove('is-invalid');
                }

                updateSubmitButtonState(form);
            });
        });

        var checked = modeInputs.find(function (input) {
            return input.checked;
        });
        setMemberPanel(checked ? checked.value : 'none');

        if (orderModal) {
            orderModal.addEventListener('shown.bs.modal', function () {
                resetLoyaltyConfirm(form);
                setMemberPanel(
                    (modeInputs.find(function (input) {
                        return input.checked;
                    }) || {}).value || 'none'
                );
                updateSubmitButtonState(form);
            });
        }

        var customerNameInput = qs('#customerName', form);
        if (customerNameInput) {
            customerNameInput.addEventListener('input', function () {
                customerNameInput.classList.remove('is-invalid');
                updateSubmitButtonState(form);
            });
        }

        var newPhoneInput = qs('#memberPhoneNew', form);
        if (newPhoneInput) {
            newPhoneInput.addEventListener('input', function () {
                newPhoneInput.classList.remove('is-invalid');
                updateSubmitButtonState(form);
            });
        }

        var existingPhoneInput = qs('#memberPhoneExisting', form);
        if (existingPhoneInput) {
            existingPhoneInput.addEventListener('input', function () {
                renderMemberResult(null, form);
                if (resultEl) {
                    resultEl.classList.remove('is-invalid');
                }
                updateSubmitButtonState(form);
            });
        }

        if (lookupBtn) {
            lookupBtn.addEventListener('click', function () {
                var memberPhoneInput = activePhoneInput('existing');
                lookupMember(
                    memberPhoneInput ? memberPhoneInput.value.trim() : '',
                    function (customer) {
                        renderMemberResult(customer, form);
                        if (resultEl) {
                            resultEl.classList.remove('is-invalid');
                        }
                        updateSubmitButtonState(form);
                    },
                    function (message) {
                        renderMemberResult(null, form);
                        showToast(message);
                        if (resultEl) {
                            resultEl.hidden = false;
                            resultEl.classList.add('is-invalid');
                            resultEl.innerHTML = '<span class="text-danger small">' + message + '</span>';
                        }
                        updateSubmitButtonState(form);
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

        if (submitBtn) {
            submitBtn.addEventListener('click', function (event) {
                if (!canProceedCheckout(form)) {
                    event.preventDefault();
                }
            });
        }

        updateSubmitButtonState(form);

        form.addEventListener('submit', function (event) {
            if (!canProceedCheckout(form)) {
                event.preventDefault();
                return;
            }

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

                if (
                    activeMember &&
                    Number(activeMember.loyalty_discount_percent || 0) > 0 &&
                    loyaltyChoice === null
                ) {
                    event.preventDefault();
                    showToast('Konfirmasi penggunaan poin terlebih dahulu (Ya atau Tidak).');
                    getLoyaltyConfirmEl()?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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
                    return;
                }

                qs('#useLoyaltyDiscount', form).value = '0';
            }

            if (mode === 'none') {
                qs('#useLoyaltyDiscount', form).value = '0';
            }

            if (mode === 'existing' && (!activeMember || Number(activeMember.loyalty_discount_percent || 0) <= 0)) {
                qs('#useLoyaltyDiscount', form).value = '0';
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMemberCheckout);
    } else {
        initMemberCheckout();
    }
})();
