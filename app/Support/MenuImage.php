<?php

namespace App\Support;

class MenuImage
{
    /** @var array<string, string> */
    private const BY_CODE = [
        'MN-001' => 'img/menus/indomie-telur.svg',
        'MN-002' => 'img/menus/indomie-double.svg',
        'MN-003' => 'img/menus/indomie-kapal-api.svg',
        'MN-004' => 'img/menus/nutrisari-dingin.svg',
        'MN-005' => 'img/menus/teh-manis-panas.svg',
        'MN-006' => 'img/menus/kopi-susu.svg',
        'MN-007' => 'img/menus/indomie-goreng-spesial.svg',
        'MN-008' => 'img/menus/roti-bakar-keju.svg',
        'MN-009' => 'img/menus/nasi-goreng-warkop.svg',
        'MN-010' => 'img/menus/es-teh-manis.svg',
        'MN-011' => 'img/menus/pisang-goreng.svg',
        'MN-012' => 'img/menus/sosis-goreng.svg',
    ];

    public static function resolve(object $menu): string
    {
        $stored = trim((string) ($menu->img_url ?? ''));

        if ($stored !== '') {
            $inventoryUrl = self::normalizeStoredInventoryImage($stored);

            if ($inventoryUrl) {
                return $inventoryUrl;
            }

            if (! self::isGenericPlaceholder($stored)) {
                return MenuAsset::url($stored);
            }
        }

        return self::defaultImage($menu);
    }

    public static function resolveFromRemote(
        ?string $code,
        ?string $name,
        ?string $category = null,
        ?string $imageUrl = null,
    ): string {
        if ($imageUrl) {
            $normalized = self::normalizeStoredInventoryImage($imageUrl);

            if ($normalized) {
                return $normalized;
            }
        }

        return self::defaultImage((object) [
            'inventory_menu_code' => $code,
            'name' => $name,
            'category' => $category,
            'description' => '',
        ]);
    }

    public static function defaultImage(object $menu): string
    {
        $code = (string) ($menu->inventory_menu_code ?? '');

        if ($code !== '' && isset(self::BY_CODE[$code])) {
            return self::BY_CODE[$code];
        }

        return self::guessFromName(
            (string) ($menu->name ?? ''),
            MenuCatalog::isBundle($menu) ? MenuCatalog::SEGMENT_BUNDLE : MenuCatalog::segment($menu)
        );
    }

    private static function normalizeStoredInventoryImage(string $stored): ?string
    {
        if (str_starts_with($stored, '/menus/images/')) {
            return MenuAsset::normalizeInventoryUrl($stored);
        }

        if (self::isRemoteUrl($stored)) {
            return MenuAsset::normalizeInventoryUrl($stored) ?? $stored;
        }

        return null;
    }

    private static function isRemoteUrl(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
    }

    private static function isGenericPlaceholder(string $path): bool
    {
        return str_contains($path, 'item_placeholder')
            || str_contains($path, 'menu_placeholder')
            || str_contains($path, 'coffee_placeholder');
    }

    private static function guessFromName(string $name, string $segment): string
    {
        $lower = strtolower($name);

        if (preg_match('/indomie.*kapal|kapal.*indomie/', $lower)) {
            return self::BY_CODE['MN-003'];
        }

        if (str_contains($lower, 'indomie') && str_contains($lower, 'telur')) {
            return self::BY_CODE['MN-001'];
        }

        if (str_contains($lower, 'indomie') && str_contains($lower, 'double')) {
            return self::BY_CODE['MN-002'];
        }

        if (str_contains($lower, 'indomie')) {
            return self::BY_CODE['MN-007'];
        }

        if (str_contains($lower, 'nasi goreng')) {
            return self::BY_CODE['MN-009'];
        }

        if (str_contains($lower, 'roti')) {
            return self::BY_CODE['MN-008'];
        }

        if (str_contains($lower, 'pisang')) {
            return self::BY_CODE['MN-011'];
        }

        if (str_contains($lower, 'sosis')) {
            return self::BY_CODE['MN-012'];
        }

        if (str_contains($lower, 'nutrisari')) {
            return self::BY_CODE['MN-004'];
        }

        if (str_contains($lower, 'es teh') || str_contains($lower, 'teh manis')) {
            return str_contains($lower, 'es') ? self::BY_CODE['MN-010'] : self::BY_CODE['MN-005'];
        }

        if (str_contains($lower, 'kopi') || str_contains($lower, 'coffee')) {
            return self::BY_CODE['MN-006'];
        }

        return match ($segment) {
            MenuCatalog::SEGMENT_BUNDLE => 'img/menus/indomie-kapal-api.svg',
            MenuCatalog::SEGMENT_DRINK => 'img/menus/kopi-susu.svg',
            default => 'img/menus/indomie-telur.svg',
        };
    }
}
