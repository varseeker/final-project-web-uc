@php
    $config = $item->option_config ?? \App\Support\MenuOptions::forMenu($item);
    $optionHints = \App\Support\MenuOptions::summaryLabels($config);
    $modalId = 'menuModal'.$item->id;
    $segmentMeta = $segmentMeta ?? \App\Support\MenuCatalog::meta()[$segmentKey ?? 'food'] ?? [];
@endphp

<div class="col">
    <button type="button"
            class="pos-menu-card w-100 text-start pos-menu-card--{{ $segmentKey ?? 'food' }}"
            data-bs-toggle="modal"
            data-bs-target="#{{ $modalId }}"
            data-menu-segment="{{ $segmentKey ?? 'food' }}"
            aria-label="Pesan {{ $item->name }}">
        <div class="pos-menu-card__img-wrap">
            @include('partials.pos.menu-display-image', ['item' => $item, 'variant' => 'card', 'class' => 'pos-menu-card__img'])
            @if($segmentKey === 'bundle' || ($item->is_bundle ?? false))
                <span class="badge bg-warning text-dark pos-menu-card__badge pos-menu-card__badge--bundle">
                    <i class="bi bi-box-seam"></i> Paket
                </span>
            @elseif($item->most_ordered ?? false)
                <span class="badge bg-danger pos-menu-card__badge">Favorit</span>
            @endif
        </div>
        <div class="pos-menu-card__body">
            <span class="pos-menu-card__segment">{{ $segmentMeta['label'] ?? '' }}</span>
            <h3 class="pos-menu-card__title">{{ $item->name }}</h3>
            <p class="pos-menu-card__price mb-2">Rp{{ number_format($item->price, 0, ',', '.') }}</p>
            @if(!empty($item->description))
                <p class="pos-menu-card__desc small text-muted mb-2">{{ Str::limit($item->description, 60) }}</p>
            @endif
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
                    @include('partials.pos.menu-display-image', [
                        'item' => $item,
                        'variant' => 'thumb',
                        'class' => 'pos-menu-modal__thumb rounded',
                        'frameClass' => 'menu-display-img-frame--modal',
                        'alt' => '',
                    ])
                    <div>
                        <div class="d-flex flex-wrap gap-1 mb-1">
                            <span class="badge rounded-pill pos-menu-modal__segment pos-menu-modal__segment--{{ $segmentKey ?? 'food' }}">
                                {{ $segmentMeta['label'] ?? '' }}
                            </span>
                            @if($segmentKey === 'bundle' || ($item->is_bundle ?? false))
                                <span class="badge rounded-pill bg-warning text-dark">Paket & Bundle</span>
                            @endif
                        </div>
                        <h4 class="modal-title fw-bold mb-1" id="{{ $modalId }}Label">{{ $item->name }}</h4>
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
