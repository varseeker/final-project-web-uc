@php
    $imgUrl = $url ?? ($item->display_img_url ?? '');
    $thumbUrl = $thumb ?? ($item->display_img_thumb_url ?? $imgUrl);
    $variant = $variant ?? 'card';
    $width = $variant === 'thumb' ? 96 : 480;
    $height = $variant === 'thumb' ? 96 : 360;
    $class = trim(($variant === 'thumb' ? 'menu-display-img menu-display-img--thumb ' : 'menu-display-img ') . ($class ?? ''));
@endphp

@if($variant === 'thumb')
    <span class="menu-display-img-frame menu-display-img-frame--thumb {{ $frameClass ?? '' }}">
        <img src="{{ $thumbUrl }}"
             alt="{{ $alt ?? '' }}"
             class="{{ $class }}"
             width="{{ $width }}"
             height="{{ $height }}"
             loading="lazy"
             decoding="async"
             data-fallback="{{ asset('img/item_placeholder.png') }}">
    </span>
@else
    <div class="menu-display-img-frame {{ $frameClass ?? '' }}">
        <img src="{{ $imgUrl }}"
             alt="{{ $alt ?? ($item->name ?? 'Menu') }}"
             class="{{ $class }}"
             width="{{ $width }}"
             height="{{ $height }}"
             loading="lazy"
             decoding="async"
             data-fallback="{{ asset('img/item_placeholder.png') }}">
    </div>
@endif
