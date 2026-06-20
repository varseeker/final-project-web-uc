<?php

namespace App\Services\Midtrans;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class SnapTokenService
{
    public function configured(): bool
    {
        $serverKey = (string) config('midtrans.server_key');
        $clientKey = (string) config('midtrans.client_key');

        return $serverKey !== ''
            && $clientKey !== ''
            && ! str_contains($serverKey, 'your-server-key')
            && ! str_contains($clientKey, 'your-client-key');
    }

    /**
     * @param  Collection<int, object>  $items
     */
    public function create(int $posOrderId, int $grossAmount, Collection $items, string $customerName): string
    {
        $this->applyConfig();

        $transactionId = sprintf('KAYU-%d-%s', $posOrderId, now()->format('YmdHis'));

        $params = [
            'transaction_details' => [
                'order_id' => $transactionId,
                'gross_amount' => max(1, $grossAmount),
            ],
            'customer_details' => [
                'first_name' => $customerName !== '' ? $customerName : 'Pelanggan',
            ],
            'item_details' => $this->buildItemDetails($items, $grossAmount),
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (\Throwable $e) {
            Log::error('Midtrans Snap token failed.', [
                'pos_order_id' => $posOrderId,
                'gross_amount' => $grossAmount,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function snapScriptUrl(): string
    {
        return config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    private function applyConfig(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        Config::$is3ds = (bool) config('midtrans.is_3ds');
    }

    /**
     * @param  Collection<int, object>  $items
     * @return list<array<string, int|string>>
     */
    private function buildItemDetails(Collection $items, int $grossAmount): array
    {
        $details = $items->values()->map(function ($item, $index) {
            $quantity = max(1, (int) ($item->quantity ?? 1));
            $price = (int) ($item->price ?? 0);
            $subtotal = (int) ($item->subtotal ?? ($price * $quantity));

            return [
                'id' => (string) ($item->menu_id ?? ('item-'.($index + 1))),
                'price' => $price > 0 ? $price : max(1, (int) round($subtotal / $quantity)),
                'quantity' => $quantity,
                'name' => $this->truncate((string) ($item->name ?? 'Menu'), 50),
            ];
        })->all();

        if ($details !== []) {
            return $details;
        }

        return [[
            'id' => 'order-total',
            'price' => max(1, $grossAmount),
            'quantity' => 1,
            'name' => 'Pesanan Warkop Kayu',
        ]];
    }

    private function truncate(string $value, int $max): string
    {
        return mb_strlen($value) > $max ? mb_substr($value, 0, $max - 1).'…' : $value;
    }
}
