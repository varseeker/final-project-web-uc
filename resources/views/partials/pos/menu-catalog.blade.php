@php
    $catalog = $menuCatalog ?? ['groups' => collect(), 'meta' => \App\Support\MenuCatalog::meta(), 'total' => 0];
    $groups = $catalog['groups'] ?? [];
    $meta = $catalog['meta'] ?? \App\Support\MenuCatalog::meta();
    $segmentOrder = ['bundle', 'drink', 'food'];
    $filters = [
        'all' => ['label' => 'Semua', 'icon' => 'bi-grid-3x3-gap'],
        'bundle' => $meta['bundle'],
        'drink' => $meta['drink'],
        'food' => $meta['food'],
    ];
@endphp

<div class="pos-menu-toolbar mb-4">
    <h1 class="h4 fw-bold mb-2 page-header mb-0">Pilih Menu</h1>
    <p class="text-muted small mb-3">Pilih segment minuman, makanan, atau paket bundle sesuai kebutuhan pelanggan.</p>

    @if(($catalog['total'] ?? 0) > 0)
        <div class="pos-segment-nav" role="tablist" aria-label="Filter kategori menu">
            @foreach ($filters as $key => $filter)
                <button type="button"
                        class="pos-segment-nav__btn @if($key === 'all') is-active @endif"
                        data-menu-filter="{{ $key }}"
                        role="tab"
                        aria-selected="{{ $key === 'all' ? 'true' : 'false' }}">
                    <i class="bi {{ $filter['icon'] ?? 'bi-circle' }}"></i>
                    <span>{{ $filter['label'] }}</span>
                    @if($key !== 'all')
                        <span class="pos-segment-nav__count">{{ ($groups[$key] ?? collect())->count() }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    @endif
</div>

@if(($catalog['total'] ?? 0) === 0)
    <div class="empty-state">
        <i class="bi bi-cup-hot"></i>
        <p class="fw-semibold mb-1">Belum ada menu tersedia</p>
        <p class="text-muted small mb-0">
            @if(config('inventory.enabled'))
                Pastikan menu aktif di Inventory Management dan env <code>INVENTORY_SERVICE_URL</code> / <code>INVENTORY_API_TOKEN</code> sudah benar, lalu refresh halaman.
            @else
                Integrasi inventory belum aktif. Set <code>INVENTORY_SERVICE_ENABLED=true</code> di environment POS.
            @endif
        </p>
    </div>
@else
    @foreach ($segmentOrder as $segmentKey)
        @php
            $items = $groups[$segmentKey] ?? collect();
            $segmentMeta = $meta[$segmentKey] ?? [];
        @endphp
        @if($items->isNotEmpty())
            <section class="pos-menu-segment mb-4"
                     data-menu-segment="{{ $segmentKey }}"
                     id="segment-{{ $segmentKey }}">
                <header class="pos-menu-segment__header">
                    <div class="pos-menu-segment__icon">
                        <i class="bi {{ $segmentMeta['icon'] ?? 'bi-tag' }}"></i>
                    </div>
                    <div>
                        <h2 class="pos-menu-segment__title">{{ $segmentMeta['label'] ?? $segmentKey }}</h2>
                        @if(!empty($segmentMeta['description']))
                            <p class="pos-menu-segment__desc mb-0">{{ $segmentMeta['description'] }}</p>
                        @endif
                    </div>
                    <span class="pos-menu-segment__badge">{{ $items->count() }} menu</span>
                </header>

                <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3">
                    @foreach ($items as $item)
                        @include('partials.pos.menu-catalog-item', [
                            'item' => $item,
                            'segmentKey' => $segmentKey,
                            'segmentMeta' => $segmentMeta,
                        ])
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach
@endif
