<?php

namespace App\Services\Inventory;

use Illuminate\Http\Client\RequestException;
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
        try {
            $response = $this->request()
                ->post('/api/orders', $payload)
                ->throw();

            return $response->json();
        } catch (RequestException $exception) {
            $response = $exception->response;

            Log::error('Inventory order push HTTP error.', [
                'status' => $response?->status(),
                'body' => $response?->json() ?? $response?->body(),
                'external_order_id' => $payload['external_order_id'] ?? null,
            ]);

            throw $exception;
        }
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
