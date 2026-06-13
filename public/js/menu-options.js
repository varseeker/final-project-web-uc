(function () {
    'use strict';

    function applyVisibleWhen(container) {
        var menuId = container.getAttribute('data-menu-id');
        var groups = container.querySelectorAll('[data-visible-when-field]');

        groups.forEach(function (group) {
            var triggerField = group.getAttribute('data-visible-when-field');
            var values = (group.getAttribute('data-visible-when-values') || '')
                .split(',')
                .map(function (v) { return v.trim(); })
                .filter(Boolean);

            var trigger = container.querySelector('[name="' + triggerField + '-' + menuId + '"]');
            if (!trigger) {
                group.style.display = 'none';
                return;
            }

            var current = trigger.type === 'radio'
                ? (container.querySelector('[name="' + triggerField + '-' + menuId + '"]:checked') || {}).value
                : trigger.value;

            var show = values.indexOf(current) !== -1;
            group.style.display = show ? '' : 'none';

            var fieldName = group.getAttribute('data-option-field');
            var hiddenId = fieldName + '-hidden-' + menuId;

            group.querySelectorAll('input, select').forEach(function (input) {
                input.disabled = !show;
            });

            var existingHidden = container.querySelector('#' + hiddenId);
            if (!show) {
                if (!existingHidden) {
                    var hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = fieldName + '-' + menuId;
                    hidden.id = hiddenId;
                    hidden.value = '-';
                    container.appendChild(hidden);
                }
            } else if (existingHidden) {
                existingHidden.remove();
            }
        });
    }

    function initMenuOptionForm(container) {
        applyVisibleWhen(container);

        if (container.dataset.bound === '1') {
            return;
        }
        container.dataset.bound = '1';

        container.addEventListener('change', function (e) {
            if (e.target.matches('[data-menu-option-trigger], select, input[type="radio"]')) {
                applyVisibleWhen(container);
            }
        });
    }

    function initMenuOptionForms() {
        document.querySelectorAll('.menu-options-form').forEach(initMenuOptionForm);
    }

    function initPosMenuModals() {
        document.querySelectorAll('.menu-order-modal').forEach(function (modalEl) {
            modalEl.addEventListener('shown.bs.modal', function () {
                var form = modalEl.querySelector('.menu-options-form');
                if (form) {
                    initMenuOptionForm(form);
                    applyVisibleWhen(form);
                }
            });
        });
    }

    function fillAdminPreset(selectEl) {
        var card = selectEl.closest('.menu-admin-options');
        if (!card) return;

        var presets = {};
        try {
            presets = JSON.parse(card.getAttribute('data-category-presets') || '{}');
        } catch (e) {
            return;
        }

        var category = selectEl.value;
        var preset = presets[category];
        if (!preset) return;

        function setField(name, value) {
            var el = card.querySelector('[name="' + name + '"]');
            if (el) el.value = value;
        }

        function setCheck(name, checked) {
            var el = card.querySelector('[name="' + name + '"]');
            if (el) el.checked = checked;
        }

        if (preset.variant && preset.variant.options) {
            setField('variant_options', preset.variant.options.join(', '));
        }
        setCheck('enable_size', !!(preset.size && preset.size.enabled));
        if (preset.size && preset.size.options) {
            setField('size_options', preset.size.options.join(', '));
            setField('size_default', preset.size.default || '');
        }
        setCheck('enable_ice', !!(preset.ice && preset.ice.enabled));
        if (preset.ice && preset.ice.options) {
            setField('ice_options', preset.ice.options.join(', '));
            setField('ice_default', preset.ice.default || '');
            if (preset.ice.visible_when) {
                setField('ice_visible_values', (preset.ice.visible_when.values || []).join(', '));
            }
        }
        setCheck('enable_sugar', !!(preset.sugar && preset.sugar.enabled));
        if (preset.sugar && preset.sugar.options) {
            setField('sugar_label', preset.sugar.label || 'Sweetness');
            setField('sugar_options', preset.sugar.options.join(', '));
            setField('sugar_default', preset.sugar.default || '');
        }
    }

    function initAdminCategoryPresets() {
        document.querySelectorAll('.menu-admin-options').forEach(function (card) {
            var categorySelect = card.closest('form') && card.closest('form').querySelector('[name="category"]');
            if (!categorySelect) return;

            categorySelect.addEventListener('change', function () {
                fillAdminPreset(categorySelect);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initMenuOptionForms();
        initPosMenuModals();
        initAdminCategoryPresets();
    });
})();
