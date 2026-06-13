@php
    $form = $form ?? \App\Support\MenuOptions::toAdminForm($optionsJson ?? null, $category ?? 'Coffee');
@endphp

<div class="card border-0 shadow-sm mb-4 menu-admin-options" data-category-presets="{{ \App\Support\MenuOptions::categoryPresetsJson() }}">
    <div class="card-header fw-bold" style="background: var(--surface-warm); color: var(--primary-color);">
        Opsi Pesanan (dari database)
    </div>
    <div class="card-body">
        <p class="small text-muted">Pisahkan opsi dengan koma. Kosongkan variant untuk pakai preset kategori.</p>

        <div class="mb-3">
            <label class="form-label fw-semibold">Variant</label>
            <input type="text" class="form-control" name="variant_options" value="{{ $form['variant_options'] }}" placeholder="Hot, Cold">
        </div>

        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="enable_size" id="enable_size" value="1" @checked($form['enable_size'])>
            <label class="form-check-label" for="enable_size">Aktifkan pilihan Size</label>
        </div>
        <div class="mb-3">
            <input type="text" class="form-control mb-1" name="size_options" value="{{ $form['size_options'] }}" placeholder="Small, Reguler, Large">
            <input type="text" class="form-control form-control-sm" name="size_default" value="{{ $form['size_default'] }}" placeholder="Default size (opsional)">
        </div>

        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="enable_ice" id="enable_ice" value="1" @checked($form['enable_ice'])>
            <label class="form-check-label" for="enable_ice">Aktifkan pilihan Ice</label>
        </div>
        <div class="mb-3">
            <input type="text" class="form-control mb-1" name="ice_options" value="{{ $form['ice_options'] }}" placeholder="No Ice, Less Ice, Normal Ice">
            <input type="text" class="form-control form-control-sm mb-1" name="ice_default" value="{{ $form['ice_default'] }}" placeholder="Default ice">
            <input type="text" class="form-control form-control-sm" name="ice_visible_values" value="{{ $form['ice_visible_values'] }}" placeholder="Tampil jika variant: Cold">
        </div>

        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="enable_sugar" id="enable_sugar" value="1" @checked($form['enable_sugar'])>
            <label class="form-check-label" for="enable_sugar">Aktifkan pilihan Sweetness</label>
        </div>
        <div class="mb-0">
            <input type="text" class="form-control mb-1" name="sugar_label" value="{{ $form['sugar_label'] }}" placeholder="Label (mis. Sweetness)">
            <input type="text" class="form-control mb-1" name="sugar_options" value="{{ $form['sugar_options'] }}" placeholder="No Sugar, Less Sugar, Normal Sugar">
            <input type="text" class="form-control form-control-sm" name="sugar_default" value="{{ $form['sugar_default'] }}" placeholder="Default sugar">
        </div>
    </div>
</div>
