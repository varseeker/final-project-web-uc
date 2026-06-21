<?php

namespace Tests\Unit;

use App\Services\Customer\CustomerMembershipService;
use PHPUnit\Framework\TestCase;

class CustomerMembershipServiceTest extends TestCase
{
    private CustomerMembershipService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CustomerMembershipService;
    }

    public function test_discount_percent_increases_every_thousand_points(): void
    {
        $this->assertSame(0, $this->service->calculateDiscountPercent(999));
        $this->assertSame(10, $this->service->calculateDiscountPercent(1000));
        $this->assertSame(10, $this->service->calculateDiscountPercent(1999));
        $this->assertSame(20, $this->service->calculateDiscountPercent(2000));
        $this->assertSame(50, $this->service->calculateDiscountPercent(5000));
        $this->assertSame(50, $this->service->calculateDiscountPercent(10000));
    }

    public function test_discount_amount_applied_to_subtotal(): void
    {
        $discount = $this->service->calculateDiscount(100_000, 3000);

        $this->assertSame(30, $discount['percent']);
        $this->assertSame(30_000, $discount['amount']);
        $this->assertSame(70_000, $discount['total']);
    }
}
