<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MenuImageOptimizer
{
    public const VARIANT_CARD = 'card';

    public const VARIANT_THUMB = 'thumb';

    public static function url(?string $source, string $variant = self::VARIANT_CARD): string
    {
        if (! $source) {
            return asset('img/item_placeholder.png');
        }

        $source = MenuAsset::normalizeInventoryUrl($source) ?? $source;

        if (self::shouldServeDirectly($source) || self::isInventoryRemoteUrl($source)) {
            return MenuAsset::url($source);
        }

        return route('media.menu.show', [
            'v' => $variant,
            'src' => self::encodeSource($source),
        ]);
    }

    public static function render(string $source, string $variant): BinaryFileResponse
    {
        $source = MenuAsset::normalizeInventoryUrl($source) ?? $source;

        self::assertAllowedSource($source);
        self::assertVariant($variant);

        $cachePath = self::cachePath($source, $variant);

        if (! is_file($cachePath)) {
            self::buildCache($source, $variant, $cachePath);
        }

        return response()->file($cachePath, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=604800, stale-while-revalidate=86400',
        ]);
    }

    public static function encodeSource(string $source): string
    {
        return rtrim(strtr(base64_encode($source), '+/', '-_'), '=');
    }

    public static function decodeSource(string $encoded): string
    {
        $normalized = strtr($encoded, '-_', '+/');
        $padding = strlen($normalized) % 4;

        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($normalized, true);

        if ($decoded === false) {
            abort(404);
        }

        return $decoded;
    }

    private static function shouldServeDirectly(string $source): bool
    {
        $lower = strtolower($source);

        return str_ends_with($lower, '.svg')
            || str_contains($lower, 'item_placeholder')
            || str_contains($lower, 'menu_placeholder');
    }

    private static function isInventoryRemoteUrl(string $source): bool
    {
        if (! str_starts_with($source, 'http://') && ! str_starts_with($source, 'https://')) {
            return false;
        }

        $sourceHost = strtolower((string) parse_url($source, PHP_URL_HOST));
        $inventoryHost = strtolower((string) parse_url((string) config('inventory.base_url'), PHP_URL_HOST));

        if ($sourceHost === '' || $inventoryHost === '') {
            return false;
        }

        return $sourceHost === $inventoryHost;
    }

    private static function assertVariant(string $variant): void
    {
        if (! array_key_exists($variant, config('menu.display', []))) {
            abort(404);
        }
    }

    private static function assertAllowedSource(string $source): void
    {
        if (str_starts_with($source, 'http://') || str_starts_with($source, 'https://')) {
            $allowedHosts = self::allowedInventoryHosts();
            $host = parse_url($source, PHP_URL_HOST);

            if (! $host || ! in_array(strtolower($host), $allowedHosts, true)) {
                abort(403);
            }

            return;
        }

        $normalized = ltrim(str_replace('\\', '/', $source), '/');

        if (! str_starts_with($normalized, 'img/')) {
            abort(403);
        }

        $path = public_path($normalized);

        if (! is_file($path)) {
            abort(404);
        }
    }

    private static function cachePath(string $source, string $variant): string
    {
        return storage_path('app/menu-cache/'.hash('sha256', $source.'|'.$variant).'.webp');
    }

    private static function buildCache(string $source, string $variant, string $cachePath): void
    {
        if (! function_exists('imagewebp')) {
            self::copyFallback($source, $variant, $cachePath);

            return;
        }

        $image = self::loadImage($source);

        if (! $image instanceof \GdImage) {
            self::copyFallback($source, $variant, $cachePath);

            return;
        }

        $settings = config("menu.display.{$variant}");
        $resized = self::resizeCover($image, (int) $settings['width'], (int) $settings['height']);

        self::ensureCacheDir(dirname($cachePath));

        $saved = imagewebp($resized, $cachePath, (int) $settings['quality']);

        imagedestroy($resized);
        imagedestroy($image);

        if (! $saved || ! is_file($cachePath)) {
            @unlink($cachePath);
            self::copyFallback($source, $variant, $cachePath);
        }
    }

    private static function loadImage(string $source): ?\GdImage
    {
        try {
            if (str_starts_with($source, 'http://') || str_starts_with($source, 'https://')) {
                $response = Http::timeout(8)->retry(1, 200)->get($source);

                if (! $response->successful()) {
                    return null;
                }

                $image = @imagecreatefromstring($response->body());

                return $image instanceof \GdImage ? $image : null;
            }

            $path = public_path(ltrim(str_replace('\\', '/', $source), '/'));

            if (! is_file($path)) {
                return null;
            }

            $image = @match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
                'jpg', 'jpeg' => imagecreatefromjpeg($path),
                'png' => imagecreatefrompng($path),
                'webp' => imagecreatefromwebp($path),
                'gif' => imagecreatefromgif($path),
                default => null,
            };

            return $image instanceof \GdImage ? $image : null;
        } catch (\Throwable $e) {
            Log::warning('Menu image optimization failed to load source.', [
                'source' => $source,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private static function resizeCover(\GdImage $source, int $targetWidth, int $targetHeight): \GdImage
    {
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $targetRatio = $targetWidth / $targetHeight;
        $sourceRatio = $sourceWidth / max(1, $sourceHeight);

        if ($sourceRatio > $targetRatio) {
            $cropHeight = $sourceHeight;
            $cropWidth = (int) round($sourceHeight * $targetRatio);
            $cropX = (int) max(0, floor(($sourceWidth - $cropWidth) / 2));
            $cropY = 0;
        } else {
            $cropWidth = $sourceWidth;
            $cropHeight = (int) round($sourceWidth / $targetRatio);
            $cropX = 0;
            $cropY = (int) max(0, floor(($sourceHeight - $cropHeight) / 2));
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        $fill = imagecolorallocate($canvas, 243, 240, 238);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $fill);

        imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            $cropX,
            $cropY,
            $targetWidth,
            $targetHeight,
            max(1, $cropWidth),
            max(1, $cropHeight)
        );

        return $canvas;
    }

    private static function copyFallback(string $source, string $variant, string $cachePath): void
    {
        self::ensureCacheDir(dirname($cachePath));
        $settings = config("menu.display.{$variant}", config('menu.display.card'));

        $placeholder = self::loadImage('img/item_placeholder.png');

        if ($placeholder instanceof \GdImage && function_exists('imagewebp')) {
            $resized = self::resizeCover($placeholder, (int) $settings['width'], (int) $settings['height']);
            imagewebp($resized, $cachePath, (int) $settings['quality']);
            imagedestroy($resized);
            imagedestroy($placeholder);

            return;
        }

        if ($placeholder instanceof \GdImage) {
            imagedestroy($placeholder);
        }
    }

    private static function ensureCacheDir(string $directory): void
    {
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    /** @return list<string> */
    private static function allowedInventoryHosts(): array
    {
        $hosts = [];

        foreach ([config('app.url'), config('inventory.base_url')] as $baseUrl) {
            $host = parse_url((string) $baseUrl, PHP_URL_HOST);

            if (! $host) {
                continue;
            }

            $hosts[] = strtolower($host);

            if ($host === 'localhost') {
                $hosts[] = '127.0.0.1';
            }

            if ($host === '127.0.0.1') {
                $hosts[] = 'localhost';
            }
        }

        return array_values(array_unique($hosts));
    }
}
