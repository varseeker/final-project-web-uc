<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryOrderPushService
{
    public function __construct(
        private InventoryClient $client,
    ) {}

    public function pushPaidOrder(int|string $orderId, string $paymentMethod): void
    {
        if (! $this->client->enabled()) {
            Log::warning('Inventory order push skipped: integration disabled or not configured.', [
                'order_id' => $orderId,
                'enabled' => (bool) config('inventory.enabled'),
                'base_url' => config('inventory.base_url'),
                'has_token' => (bool) config('inventory.api_token'),
            ]);

            return;
        }

        $order = DB::table('order')->where('id', $orderId)->first();

        if (! $order) {
            Log::warning('Inventory order push skipped: order not found.', ['order_id' => $orderId]);

            return;
        }

        $cashierName = \App\Models\User::query()->where('id', $order->user_id)->value('name');

        $payment = DB::table('payment')
            ->where('order_id', $orderId)
            ->orderByDesc('id')
            ->first();

        $items = DB::table('ordered_items')
            ->join('menus', 'ordered_items.menu_id', '=', 'menus.id')
            ->where('ordered_items.order_id', $orderId)
            ->select(
                'menus.inventory_menu_code',
                'menus.name as menu_name',
                'menus.price as menu_price',
                'ordered_items.quantity',
                'ordered_items.variant',
                'ordered_items.size',
                'ordered_items.ice',
                'ordered_items.sugar',
                'ordered_items.subtotal',
                'ordered_items.status',
            )
            ->get();

        $payloadItems = [];

        foreach ($items as $item) {
            $menuCode = trim((string) ($item->inventory_menu_code ?? ''));

            if ($menuCode === '') {
                Log::warning('Skipping inventory push item without menu code.', [
                    'order_id' => $orderId,
                    'menu_name' => $item->menu_name,
                ]);

                continue;
            }

            $payloadItems[] = [
                'menu_code' => $menuCode,
                'menu_name' => $item->menu_name,
                'menu_price' => (int) $item->menu_price,
                'quantity' => (int) $item->quantity,
                'variant' => $item->variant,
                'size' => $item->size,
                'ice' => $item->ice,
                'sugar' => $item->sugar,
                'subtotal' => (int) $item->subtotal,
                'status' => $item->status,
            ];
        }

        if ($payloadItems === []) {
            Log::warning('Inventory order push skipped: no linked menu items.', [
                'order_id' => $orderId,
                'item_count' => $items->count(),
            ]);

            return;
        }

        $payload = [
            'external_order_id' => config('inventory.order_id_prefix', 'pos-warkop-kayu').'-'.$orderId,
            'pos_order_id' => (int) $orderId,
            'source' => config('inventory.source', 'pos-warkop-kayu'),
            'customer' => $order->customer,
            'payment_method' => $paymentMethod,
            'payment_reference' => $payment->reference ?? $order->payReference ?? null,
            'cashier_name' => $cashierName,
            'order_total' => (int) $order->total,
            'amount_paid' => (int) ($order->amountPaid ?? $payment->totalPay ?? $order->total),
            'amount_change' => (int) ($order->amountChange ?? 0),
            'order_status' => $order->status,
            'payment_status' => $order->{'payment-status'},
            'ordered_at' => $order->created_at ?? now()->toIso8601String(),
            'items' => $payloadItems,
        ];

        try {
            $result = $this->client->pushOrder($payload);

            Log::info('Inventory order push succeeded.', [
                'order_id' => $orderId,
                'external_order_id' => $payload['external_order_id'],
                'status' => $result['status'] ?? 'unknown',
                'warnings' => $result['warnings'] ?? [],
            ]);
        } catch (\Throwable $e) {
            Log::error('Inventory order push failed.', [
                'order_id' => $orderId,
                'external_order_id' => $payload['external_order_id'],
                'message' => $e->getMessage(),
            ]);
        }
    }
}
