<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InventoryClient
{
    public function enabled(): bool
    {
        return (bool) config('inventory.enabled')
            && config('inventory.base_url')
            && config('inventory.api_token');
    }

    public function fetchMenus(): array
    {
        $response = $this->request()
            ->get('/api/menus')
            ->throw();

        return $response->json('data', []);
    }

    public function pushOrder(array $payload): array
    {
        $response = $this->request()
            ->post('/api/orders', $payload)
            ->throw();

        return $response->json();
    }

    private function request()
    {
        return Http::baseUrl(config('inventory.base_url'))
            ->withToken(config('inventory.api_token'))
            ->acceptJson()
            ->timeout((int) config('inventory.timeout_seconds', 30))
            ->retry(2, 1000);
    }
}
