<?php

namespace App\Services\Inventory;

use App\Support\MenuAsset;
use App\Support\MenuOptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryMenuSyncService
{
    private const CACHE_KEY = 'inventory.menu_sync.last_run';

    public function __construct(
        private InventoryClient $client,
    ) {}

    /**
     * Sinkronkan menu dari inventory jika belum ada di POS atau cache TTL habis.
     */
    public function ensureSynced(): bool
    {
        if (! $this->client->enabled()) {
            return false;
        }

        if ($this->needsImmediateSync()) {
            Cache::forget(self::CACHE_KEY);

            $result = $this->sync();

            if ($result) {
                $this->markSyncRan();
            }

            return $result;
        }

        return $this->syncIfStale();
    }

    public function syncIfStale(): bool
    {
        if (! $this->client->enabled()) {
            return false;
        }

        $ttl = config('inventory.sync_ttl_seconds', 120);

        if (Cache::has(self::CACHE_KEY)) {
            return false;
        }

        $result = $this->sync();

        if ($result) {
            $this->markSyncRan($ttl);
        }

        return $result;
    }

    public function sync(): bool
    {
        if (! $this->client->enabled()) {
            Log::warning('Inventory sync skipped: service not configured.');

            return false;
        }

        try {
            $remoteMenus = $this->client->fetchMenus();
        } catch (\Throwable $e) {
            Log::error('Inventory menu sync failed.', [
                'message' => $e->getMessage(),
                'base_url' => config('inventory.base_url'),
            ]);

            return false;
        }

        if ($remoteMenus === []) {
            Log::warning('Inventory menu sync returned empty menu list.');

            return false;
        }

        $activeCodes = [];
        $now = now();

        foreach ($remoteMenus as $remote) {
            $code = (string) ($remote['code'] ?? '');

            if ($code === '') {
                continue;
            }

            $activeCodes[] = $code;

            $existing = DB::table('menus')
                ->where('inventory_menu_code', $code)
                ->first();

            $category = $this->normalizeCategory(
                (string) ($remote['category'] ?? $existing->category ?? $this->inferCategory((string) ($remote['name'] ?? '')))
            );
            $options = $existing->options ?? json_encode(MenuOptions::defaultsForCategory($category));
            $imgUrl = $this->resolveRemoteImage($remote);
            $payload = [
                'inventory_menu_id' => $remote['id'] ?? $existing->inventory_menu_id ?? null,
                'name' => $remote['name'] ?? $existing->name ?? $code,
                'description' => $remote['description'] ?? $existing->description ?? '',
                'category' => $category,
                'price' => (int) ($remote['price'] ?? $existing->price ?? config('inventory.default_price', 18000)),
                'most_ordered' => (bool) ($remote['most_ordered'] ?? $existing->most_ordered ?? false),
                'img_url' => $imgUrl,
                'options' => $options,
                'is_active' => (bool) ($remote['is_active'] ?? true),
                'inventory_synced_at' => $now,
                'updated_at' => $now,
            ];

            if ($existing) {
                DB::table('menus')->where('id', $existing->id)->update($payload);

                continue;
            }

            DB::table('menus')->insert(array_merge($payload, [
                'inventory_menu_code' => $code,
                'created_at' => $now,
            ]));
        }

        if ($activeCodes !== []) {
            DB::table('menus')
                ->whereNotNull('inventory_menu_code')
                ->whereNotIn('inventory_menu_code', $activeCodes)
                ->update([
                    'is_active' => false,
                    'inventory_synced_at' => $now,
                    'updated_at' => $now,
                ]);
        }

        return true;
    }

    private function needsImmediateSync(): bool
    {
        $visibleCount = DB::table('menus')
            ->where('is_active', true)
            ->whereNotNull('inventory_menu_code')
            ->count();

        if ($visibleCount === 0) {
            return true;
        }

        return DB::table('menus')
            ->whereNotNull('inventory_menu_code')
            ->whereNull('inventory_synced_at')
            ->exists();
    }

    private function markSyncRan(?int $ttl = null): void
    {
        Cache::put(
            self::CACHE_KEY,
            now()->timestamp,
            $ttl ?? config('inventory.sync_ttl_seconds', 120)
        );
    }

    private function resolveRemoteImage(array $remote): string
    {
        $remoteUrl = MenuAsset::normalizeInventoryUrl($remote['image_url'] ?? null);

        if ($remoteUrl) {
            return $remoteUrl;
        }

        return 'img/item_placeholder.png';
    }

    private function normalizeCategory(string $category): string
    {
        return match ($category) {
            'Non-Coffee' => 'Non-coffee',
            default => $category,
        };
    }

    private function inferCategory(string $name): string
    {
        $lower = strtolower($name);

        if (preg_match('/\bindomie\b|\bnasi\b|\broti\b|\bsosis\b|\bpisang\b|\bmie goreng\b|\bsnack\b|\bfries\b/', $lower)) {
            return 'Snack';
        }

        if (preg_match('/\bkopi\b|\bcoffee\b|\blatte\b|\bcappuccino\b|\bespresso\b|\btea\b|\bteh\b|\bnutrisari\b|\bminuman\b|\bjuice\b|\bes teh\b|\bes\b/', $lower)) {
            return 'Non-coffee';
        }

        return 'Snack';
    }
}
