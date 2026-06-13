<?php

namespace App\Support;

use Illuminate\Support\Collection;

class MenuCatalog
{
    public const SEGMENT_BUNDLE = 'bundle';

    public const SEGMENT_DRINK = 'drink';

    public const SEGMENT_FOOD = 'food';

    /**
     * @param  Collection<int, object>  $items
     * @return array{groups: array<string, Collection>, meta: array<string, array<string, string>>, total: int}
     */
    public static function build(Collection $items, bool $withOptions = false): array
    {
        $prepared = $items->map(function ($item) use ($withOptions) {
            if ($withOptions) {
                $item->option_config = MenuOptions::forMenu($item);
            }

            $item->is_bundle = MenuCatalog::isBundle($item);
            $item->catalog_segment = $item->is_bundle
                ? MenuCatalog::SEGMENT_BUNDLE
                : MenuCatalog::segment($item);
            $item->segment_label = MenuCatalog::segmentLabel($item->catalog_segment);
            $resolved = MenuImage::resolve($item);
            $item->display_img_url = MenuImageOptimizer::url($resolved, MenuImageOptimizer::VARIANT_CARD);
            $item->display_img_thumb_url = MenuImageOptimizer::url($resolved, MenuImageOptimizer::VARIANT_THUMB);

            return $item;
        });

        return [
            'groups' => [
                self::SEGMENT_BUNDLE => $prepared->where('catalog_segment', self::SEGMENT_BUNDLE)->values(),
                self::SEGMENT_DRINK => $prepared->where('catalog_segment', self::SEGMENT_DRINK)->values(),
                self::SEGMENT_FOOD => $prepared->where('catalog_segment', self::SEGMENT_FOOD)->values(),
            ],
            'meta' => self::meta(),
            'total' => $prepared->count(),
        ];
    }

    public static function isBundle(object $menu): bool
    {
        $name = strtolower((string) ($menu->name ?? ''));
        $description = strtolower((string) ($menu->description ?? ''));

        $patterns = [
            '/\b(paket|bundle|combo|hemat|platter)\b/',
            '/indomie\s+(kapal|kopi)/',
            '/kapal\s*api.*indomie|indomie.*kapal\s*api/',
            '/nasi goreng warkop/',
            '/mie\s*\+\s*(kopi|teh|minuman)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $name) || preg_match($pattern, $description)) {
                return true;
            }
        }

        $hasFood = (bool) preg_match('/indomie|nasi|roti|mie goreng|sosis|pisang goreng/', $name);
        $hasDrink = (bool) preg_match('/kapal api|kopi|teh|nutrisari|es teh|minuman/', $name);

        return $hasFood && $hasDrink;
    }

    public static function segment(object $menu): string
    {
        $category = self::normalizeCategory($menu->category ?? null);

        if ($category === 'Snack') {
            return self::SEGMENT_FOOD;
        }

        if (in_array($category, ['Coffee', 'Non-coffee'], true)) {
            return self::SEGMENT_DRINK;
        }

        $name = strtolower((string) ($menu->name ?? ''));

        if (preg_match('/kopi|coffee|latte|cappuccino|espresso|teh|tea|nutrisari|minuman|jeruk|susu|es\s/', $name)) {
            return self::SEGMENT_DRINK;
        }

        return self::SEGMENT_FOOD;
    }

    public static function segmentLabel(string $segment): string
    {
        return self::meta()[$segment]['label'] ?? ucfirst($segment);
    }

    /**
     * @return array<string, array{label: string, icon: string, description: string}>
     */
    public static function meta(): array
    {
        return [
            self::SEGMENT_BUNDLE => [
                'label' => 'Paket & Bundle',
                'icon' => 'bi-box-seam',
                'description' => 'Kombinasi makanan dan minuman dalam satu pesanan.',
            ],
            self::SEGMENT_DRINK => [
                'label' => 'Minuman',
                'icon' => 'bi-cup-straw',
                'description' => 'Kopi, teh, dan minuman dingin.',
            ],
            self::SEGMENT_FOOD => [
                'label' => 'Makanan',
                'icon' => 'bi-egg-fried',
                'description' => 'Mie, nasi, roti, dan camilan.',
            ],
        ];
    }

    private static function normalizeCategory(?string $category): string
    {
        return match ($category) {
            'Non-Coffee' => 'Non-coffee',
            default => $category ?? 'Snack',
        };
    }
}
