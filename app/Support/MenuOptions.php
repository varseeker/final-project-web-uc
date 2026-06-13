<?php

namespace App\Support;

use Illuminate\Http\Request;

class MenuOptions
{
    public static function defaultsForCategory(?string $category): array
    {
        $category = self::normalizeCategory($category);

        return match ($category) {
            'Snack' => [
                'variant' => self::field('select', 'Variant', ['Spicy', 'Mild', 'Not Spicy'], true),
                'size' => self::field('radio', 'Size', ['Reguler', 'Large'], true, 'Reguler'),
                'ice' => self::disabledField(),
                'sugar' => self::disabledField(),
            ],
            'Non-coffee', 'Non-Coffee' => [
                'variant' => self::field('select', 'Variant', ['Hot', 'Cold'], true),
                'size' => self::field('radio', 'Size', ['Small', 'Reguler', 'Large'], true, 'Reguler'),
                'ice' => self::field('radio', 'Ice', ['No Ice', 'Less Ice', 'Normal Ice'], true, 'Normal Ice', [
                    'field' => 'variant',
                    'values' => ['Cold'],
                ]),
                'sugar' => self::field('radio', 'Sweetness', ['No Sugar', 'Less Sugar', 'Normal Sugar'], true, 'Normal Sugar'),
            ],
            default => [
                'variant' => self::field('select', 'Variant', ['Hot', 'Cold'], true),
                'size' => self::field('radio', 'Size', ['Small', 'Reguler', 'Large'], true, 'Reguler'),
                'ice' => self::field('radio', 'Ice', ['No Ice', 'Less Ice', 'Normal Ice'], true, 'Normal Ice', [
                    'field' => 'variant',
                    'values' => ['Cold'],
                ]),
                'sugar' => self::field('radio', 'Sweetness', ['No Sugar', 'Less Sugar', 'Normal Sugar'], true, 'Normal Sugar'),
            ],
        };
    }

    public static function resolve(?string $json, ?string $category): array
    {
        if ($json) {
            $decoded = json_decode($json, true);
            if (is_array($decoded) && ! empty($decoded)) {
                return self::mergeWithDefaults($decoded, $category);
            }
        }

        return self::defaultsForCategory($category);
    }

    public static function forMenu(object $menu): array
    {
        return self::resolve($menu->options ?? null, $menu->category ?? null);
    }

    public static function buildFromAdminRequest(Request $request): ?string
    {
        $variantOptions = self::parseList($request->input('variant_options', ''));
        if ($variantOptions === []) {
            return null;
        }

        $config = [
            'variant' => self::field('select', 'Variant', $variantOptions, true),
        ];

        if ($request->boolean('enable_size')) {
            $sizeOptions = self::parseList($request->input('size_options', ''));
            if ($sizeOptions !== []) {
                $config['size'] = self::field(
                    'radio',
                    'Size',
                    $sizeOptions,
                    true,
                    $request->input('size_default') ?: $sizeOptions[0]
                );
            }
        } else {
            $config['size'] = self::disabledField();
        }

        if ($request->boolean('enable_ice')) {
            $iceOptions = self::parseList($request->input('ice_options', ''));
            if ($iceOptions !== []) {
                $visibleValues = self::parseList($request->input('ice_visible_values', 'Cold'));
                $config['ice'] = self::field(
                    'radio',
                    'Ice',
                    $iceOptions,
                    true,
                    $request->input('ice_default') ?: $iceOptions[0],
                    ['field' => 'variant', 'values' => $visibleValues]
                );
            }
        } else {
            $config['ice'] = self::disabledField();
        }

        if ($request->boolean('enable_sugar')) {
            $sugarOptions = self::parseList($request->input('sugar_options', ''));
            if ($sugarOptions !== []) {
                $config['sugar'] = self::field(
                    'radio',
                    $request->input('sugar_label', 'Sweetness'),
                    $sugarOptions,
                    true,
                    $request->input('sugar_default') ?: $sugarOptions[0]
                );
            }
        } else {
            $config['sugar'] = self::disabledField();
        }

        return json_encode($config);
    }

    public static function toAdminForm(?string $json, ?string $category): array
    {
        $config = self::resolve($json, $category);

        return [
            'variant_options' => implode(', ', $config['variant']['options'] ?? []),
            'size_options' => implode(', ', $config['size']['options'] ?? []),
            'size_default' => $config['size']['default'] ?? '',
            'ice_options' => implode(', ', $config['ice']['options'] ?? []),
            'ice_default' => $config['ice']['default'] ?? '',
            'ice_visible_values' => implode(', ', $config['ice']['visible_when']['values'] ?? ['Cold']),
            'sugar_options' => implode(', ', $config['sugar']['options'] ?? []),
            'sugar_default' => $config['sugar']['default'] ?? '',
            'sugar_label' => $config['sugar']['label'] ?? 'Sweetness',
            'enable_size' => $config['size']['enabled'] ?? false,
            'enable_ice' => $config['ice']['enabled'] ?? false,
            'enable_sugar' => $config['sugar']['enabled'] ?? false,
        ];
    }

    public static function selectionsFromRequest(array $config, Request $request, int $menuId): array
    {
        $selections = [];

        foreach (['variant', 'size', 'ice', 'sugar'] as $key) {
            $field = $config[$key] ?? self::disabledField();

            if (! ($field['enabled'] ?? false)) {
                $selections[$key] = '-';
                continue;
            }

            $value = $request->input("{$key}-{$menuId}");

            if ($key === 'ice' && isset($field['visible_when'])) {
                $triggerField = $field['visible_when']['field'] ?? 'variant';
                $triggerValue = $request->input("{$triggerField}-{$menuId}");
                $allowed = $field['visible_when']['values'] ?? [];

                if (! in_array($triggerValue, $allowed, true)) {
                    $value = '-';
                }
            }

            if (! $value || $value === '') {
                $value = $field['default'] ?? ($field['options'][0] ?? '-');
            }

            if (! in_array($value, $field['options'] ?? [], true) && $value !== '-') {
                $value = $field['default'] ?? ($field['options'][0] ?? '-');
            }

            $selections[$key] = $value;
        }

        return $selections;
    }

    public static function categoryPresetsJson(): string
    {
        return json_encode([
            'Coffee' => self::defaultsForCategory('Coffee'),
            'Non-coffee' => self::defaultsForCategory('Non-coffee'),
            'Non-Coffee' => self::defaultsForCategory('Non-Coffee'),
            'Snack' => self::defaultsForCategory('Snack'),
        ]);
    }

    /** Ringkasan opsi untuk kartu menu di POS */
    public static function summaryLabels(array $config): array
    {
        $labels = [];

        foreach (['variant', 'size', 'ice', 'sugar'] as $key) {
            $field = $config[$key] ?? ['enabled' => false];
            if (! ($field['enabled'] ?? false) || empty($field['options'])) {
                continue;
            }
            $labels[] = ($field['label'] ?? ucfirst($key)).': '.implode(', ', $field['options']);
        }

        return $labels;
    }

    private static function mergeWithDefaults(array $custom, ?string $category): array
    {
        $defaults = self::defaultsForCategory($category);

        foreach (['variant', 'size', 'ice', 'sugar'] as $key) {
            if (! isset($custom[$key])) {
                $custom[$key] = $defaults[$key];
            }
        }

        return $custom;
    }

    private static function field(
        string $type,
        string $label,
        array $options,
        bool $enabled = true,
        ?string $default = null,
        ?array $visibleWhen = null
    ): array {
        $field = [
            'enabled' => $enabled,
            'label' => $label,
            'type' => $type,
            'options' => array_values($options),
            'required' => true,
            'default' => $default ?? ($options[0] ?? null),
        ];

        if ($visibleWhen) {
            $field['visible_when'] = $visibleWhen;
        }

        return $field;
    }

    private static function disabledField(): array
    {
        return [
            'enabled' => false,
            'label' => null,
            'type' => 'hidden',
            'options' => [],
            'required' => false,
            'default' => '-',
        ];
    }

    private static function parseList(string $value): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    private static function normalizeCategory(?string $category): string
    {
        return match ($category) {
            'Non-Coffee' => 'Non-coffee',
            default => $category ?? 'Coffee',
        };
    }
}
