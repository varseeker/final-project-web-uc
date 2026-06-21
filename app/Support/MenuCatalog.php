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
        return (bool) ($menu->is_bundle ?? false);
    }

    public static function segment(object $menu): string
    {
        return self::normalizeCategory($menu->category ?? null) === 'Minuman'
            ? self::SEGMENT_DRINK
            : self::SEGMENT_FOOD;
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
            'Minuman', 'Non-coffee', 'Non-Coffee', 'Coffee' => 'Minuman',
            'Makanan', 'Snack' => 'Makanan',
            default => $category ?? 'Makanan',
        };
    }
}
