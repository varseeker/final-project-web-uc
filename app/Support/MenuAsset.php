<?php

namespace App\Support;

class MenuAsset
{
    public static function url(?string $path): string
    {
        if (! $path) {
            return asset('img/item_placeholder.png');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return self::normalizeInventoryUrl($path) ?? $path;
        }

        return asset($path);
    }

    public static function normalizeInventoryUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);
        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path) || ! str_starts_with($path, '/menus/images/')) {
            return $url;
        }

        $baseUrl = rtrim((string) config('inventory.base_url'), '/');

        if ($baseUrl === '') {
            return $url;
        }

        $query = parse_url($url, PHP_URL_QUERY);

        return $baseUrl.$path.($query ? '?'.$query : '');
    }

    public static function isInventoryManaged(object $menu): bool
    {
        return (string) ($menu->inventory_menu_code ?? '') !== '';
    }
}
