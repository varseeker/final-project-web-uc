<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Support\CustomerPhone;
use Illuminate\Support\Facades\DB;

class CustomerMembershipService
{
    private const LOYALTY_RATE = 0.01;

    private const LOYALTY_DISCOUNT_STEP_POINTS = 1000;

    private const LOYALTY_DISCOUNT_STEP_PERCENT = 10;

    private const MAX_LOYALTY_DISCOUNT_PERCENT = 50;
    public function findByPhone(?string $phone): ?Customer
    {
        $normalized = CustomerPhone::normalize($phone);

        if ($normalized === null) {
            return null;
        }

        return Customer::query()->where('phone', $normalized)->first();
    }

    public function createMember(string $name, ?string $phone): Customer
    {
        $normalized = CustomerPhone::normalize($phone);

        if ($normalized === null) {
            throw new \InvalidArgumentException('Nomor telepon tidak valid.');
        }

        $existing = Customer::query()->where('phone', $normalized)->first();

        if ($existing) {
            if (trim($name) !== '' && $existing->name !== trim($name)) {
                $existing->update(['name' => trim($name)]);
            }

            return $existing->fresh();
        }

        return Customer::query()->create([
            'name' => trim($name),
            'phone' => $normalized,
            'loyalty_points' => 0,
        ]);
    }

    public function calculateEarnedPoints(int $orderTotal): int
    {
        if ($orderTotal <= 0) {
            return 0;
        }

        return (int) floor($orderTotal * self::LOYALTY_RATE);
    }

    public function calculateDiscountPercent(int $loyaltyPoints): int
    {
        if ($loyaltyPoints < self::LOYALTY_DISCOUNT_STEP_POINTS) {
            return 0;
        }

        $steps = intdiv($loyaltyPoints, self::LOYALTY_DISCOUNT_STEP_POINTS);
        $percent = $steps * self::LOYALTY_DISCOUNT_STEP_PERCENT;

        return min(self::MAX_LOYALTY_DISCOUNT_PERCENT, $percent);
    }

    /**
     * @return array{percent: int, amount: int, total: int}
     */
    public function calculateDiscount(int $subtotal, int $loyaltyPoints): array
    {
        $percent = $this->calculateDiscountPercent($loyaltyPoints);
        $amount = $percent > 0 ? (int) floor($subtotal * $percent / 100) : 0;
        $total = max(0, $subtotal - $amount);

        return [
            'percent' => $percent,
            'amount' => $amount,
            'total' => $total,
        ];
    }

    public function awardPointsForOrder(int|string $orderId): int
    {
        $order = DB::table('order')->where('id', $orderId)->first();

        if (! $order || empty($order->customer_id)) {
            return 0;
        }

        if ((int) ($order->loyalty_points_earned ?? 0) > 0) {
            return (int) $order->loyalty_points_earned;
        }

        $total = (int) ($order->total ?? 0);
        $points = $this->calculateEarnedPoints($total);

        if ($points <= 0) {
            return 0;
        }

        DB::table('customers')
            ->where('id', $order->customer_id)
            ->increment('loyalty_points', $points);

        DB::table('order')
            ->where('id', $orderId)
            ->update([
                'loyalty_points_earned' => $points,
                'updated_at' => now(),
            ]);

        return $points;
    }
}
