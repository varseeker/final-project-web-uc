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
            $allItems = collect($segmentOrder)
                ->flatMap(fn ($key) => ($groups[$key] ?? collect())->map(function ($item) use ($key) {
                    $item->catalog_segment = $key;

                    return $item;
                }));
        @endphp

        @if(($catalog['total'] ?? 0) === 0)
            <div class="empty-state empty-state--compact">
                <i class="bi bi-cup-hot"></i>
                <p class="fw-semibold mb-0">Menu belum tersedia</p>
            </div>
        @else
            <div class="pos-segment-nav welcome-segment-nav mb-3" role="tablist" aria-label="Filter menu publik">
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
                        @else
                            <span class="pos-segment-nav__count">{{ $allItems->count() }}</span>
                        @endif
                    </button>
                @endforeach
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2" id="welcome-menu-grid">
                @foreach ($allItems as $item)
                    @php
                        $segmentKey = $item->catalog_segment ?? 'food';
                        $segmentMeta = $meta[$segmentKey] ?? [];
                    @endphp
                    <div class="col welcome-menu-col" data-menu-segment="{{ $segmentKey }}">
                        <article class="welcome-menu-card welcome-menu-card--{{ $segmentKey }}">
                            <div class="welcome-menu-card__media">
                                @include('partials.pos.menu-display-image', [
                                    'item' => $item,
                                    'variant' => 'card',
                                    'class' => 'welcome-menu-card__img',
                                ])
                                @if($segmentKey === 'bundle' || ($item->is_bundle ?? false))
                                    <span class="welcome-menu-card__flag welcome-menu-card__flag--bundle">Paket</span>
                                @elseif ($item->most_ordered ?? false)
                                    <span class="welcome-menu-card__flag welcome-menu-card__flag--hot">Favorit</span>
                                @endif
                            </div>
                            <div class="welcome-menu-card__body">
                                <span class="welcome-menu-card__tag">{{ $segmentMeta['label'] ?? '' }}</span>
                                <h3 class="welcome-menu-card__title">{{ $item->name }}</h3>
                                <p class="welcome-menu-card__price mb-0">Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
