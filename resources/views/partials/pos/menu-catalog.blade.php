@php
    $categories = $groupedItems ?? collect();
@endphp

<div class="pos-menu-toolbar mb-4">
    <h1 class="h4 fw-bold mb-2 page-header mb-0">Pilih Menu</h1>
    <p class="text-muted small mb-3">Ketuk menu untuk memilih opsi sesuai konfigurasi di database.</p>
    @if($categories->isNotEmpty())
        <div class="d-flex flex-wrap gap-2 pos-category-nav">
            @foreach ($categories as $category => $items)
                <a href="#cat-{{ Str::slug($category) }}" class="btn btn-sm btn-outline-secondary">{{ $category }}</a>
            @endforeach
        </div>
    @endif
</div>

@forelse ($categories as $category => $items)
    <section class="pos-menu-section mb-4">
        <h2 class="fw-bold mb-3 category-heading" id="cat-{{ Str::slug($category) }}">{{ $category }}</h2>
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3">
            @foreach ($items as $item)
                @php
                    $config = $item->option_config ?? \App\Support\MenuOptions::forMenu($item);
                    $optionHints = \App\Support\MenuOptions::summaryLabels($config);
                    $modalId = 'menuModal'.$item->id;
                @endphp
                <div class="col">
                    <button type="button"
                            class="pos-menu-card w-100 text-start"
                            data-bs-toggle="modal"
                            data-bs-target="#{{ $modalId }}"
                            aria-label="Pesan {{ $item->name }}">
                        <div class="pos-menu-card__img-wrap">
                            <img src="{{ asset($item->img_url) }}" alt="{{ $item->name }}" class="pos-menu-card__img" loading="lazy">
                            @if($item->most_ordered)
                                <span class="badge bg-danger pos-menu-card__badge">Favorit</span>
                            @endif
                        </div>
                        <div class="pos-menu-card__body">
                            <h3 class="pos-menu-card__title">{{ $item->name }}</h3>
                            <p class="pos-menu-card__price mb-2">Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                            @if(count($optionHints) > 0)
                                <ul class="pos-menu-card__hints list-unstyled mb-0">
                                    @foreach (array_slice($optionHints, 0, 2) as $hint)
                                        <li class="small text-muted">{{ $hint }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </button>
                </div>

                <div class="modal fade menu-order-modal" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <form class="modal-content pos-menu-modal" method="POST" action="{{ url('/home/store') }}">
                            @csrf
                            <div class="modal-header pos-menu-modal__header border-0">
                                <div class="d-flex gap-3 align-items-start">
                                    <img src="{{ asset($item->img_url) }}" alt="" class="pos-menu-modal__thumb rounded">
                                    <div>
                                        <h4 class="modal-title fw-bold mb-1" id="{{ $modalId }}Label">{{ $item->name }}</h4>
                                        <p class="small text-muted mb-0">{{ $category }}</p>
                                        <p class="fw-bold mb-0" style="color: var(--primary-color);">Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <input type="hidden" name="menu_id" value="{{ $item->id }}">
                            <div class="modal-body pos-menu-modal__body">
                                @if(!empty($item->description))
                                    <p class="small text-muted mb-3">{{ $item->description }}</p>
                                @endif
                                @include('partials.pos.menu-options-form', ['item' => $item, 'config' => $config])
                            </div>
                            <div class="modal-footer pos-menu-modal__footer border-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-cart-plus me-1"></i> Tambah ke keranjang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@empty
    <div class="empty-state">
        <i class="bi bi-cup-hot"></i>
        <p class="fw-semibold mb-0">Belum ada menu tersedia</p>
    </div>
@endforelse
