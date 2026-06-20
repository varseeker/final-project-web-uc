<?php

namespace App\Http\Controllers;

use App\Services\Inventory\InventoryOrderPushService;
use App\Services\Midtrans\SnapTokenService;
use App\Services\Customer\CustomerMembershipService;
use App\Models\Customer;
use App\Support\CustomerPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function __construct(
        private InventoryOrderPushService $inventoryOrderPush,
        private SnapTokenService $snapTokenService,
        private CustomerMembershipService $customerMembership,
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function reciept(Request $request)
    {
        $orderId = $request->input('cartToDelete');
        $order = DB::table('order')->where('id', $orderId)->first();

        if (! $order) {
            return redirect('home')->with('lastAct', 'Pesanan tidak ditemukan.');
        }

        $total = (int) $this->getOrderItems($orderId)->sum('subtotal');
        $pay = (int) preg_replace('/\D/', '', (string) $request->input('cashAmount', 0));
        $change = max(0, $pay - $total);

        if ($pay < $total) {
            return redirect('home')->with('lastAct', 'Nominal tunai kurang dari total tagihan.');
        }

        $cashReference = $this->generateCashTransactionReference();

        $this->clearUserCart();
        $this->finalizeOrderPayment($orderId, [
            'status' => 'paid',
            'amountPaid' => $pay,
            'amountChange' => $change,
            'payment-status' => 'success',
            'payReference' => $cashReference,
        ], 'Cash');
        $this->recordPayment($orderId, $pay, 'Cash', $cashReference);

        return $this->receiptView(
            $orderId,
            $request->input('customerName', $order->customer),
            $pay,
            $change,
            'Tunai',
            $cashReference
        );
    }

    public function orderPage(Request $request)
    {
        $request->validate([
            'customerName' => ['required', 'string', 'max:120'],
            'memberMode' => ['nullable', 'in:none,existing,new'],
            'memberPhone' => ['nullable', 'string', 'max:20'],
            'customerId' => ['nullable', 'integer'],
        ]);

        $memberMode = (string) $request->input('memberMode', 'none');
        $csName = trim((string) $request->input('customerName'));
        $customerId = null;

        if ($memberMode === 'existing') {
            $memberPhone = trim((string) $request->input('memberPhone', ''));
            $customer = $this->customerMembership->findByPhone($memberPhone);

            if (! $customer) {
                return redirect('home')->with('lastAct', 'Member tidak ditemukan. Periksa nomor telepon atau daftar sebagai member baru.');
            }

            $customerId = (int) $customer->id;
            $csName = $customer->name;
        } elseif ($memberMode === 'new') {
            $memberPhone = trim((string) $request->input('memberPhone', ''));

            if ($memberPhone === '') {
                return redirect('home')->with('lastAct', 'Nomor telepon wajib diisi untuk mendaftar member.');
            }

            try {
                $customer = $this->customerMembership->createMember($csName, $memberPhone);
            } catch (\InvalidArgumentException $e) {
                return redirect('home')->with('lastAct', $e->getMessage());
            }

            $customerId = (int) $customer->id;
            $csName = $customer->name;
        }

        $basketOwner = DB::table('carts')
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        if (! $basketOwner) {
            return redirect('home')->with('lastAct', 'Keranjang kosong. Tambahkan menu terlebih dahulu.');
        }

        $baskets = DB::table('cart_items')
            ->where('cart_id', $basketOwner->id)
            ->join('menus', 'menu_id', '=', 'menus.id')
            ->select(
                'cart_items.id',
                'cart_id',
                'menu_id',
                'menus.name',
                'menus.description',
                'menus.price',
                'quantity',
                'variant',
                'size',
                'ice',
                'sugar',
                'subtotal'
            )
            ->get();

        if ($baskets->isEmpty()) {
            return redirect('home')->with('lastAct', 'Keranjang kosong. Tambahkan menu terlebih dahulu.');
        }

        $total = 0;
        foreach ($baskets as $item) {
            $item->subtotal = (int) ($item->subtotal ?? ($item->price * $item->quantity));
            $total += $item->subtotal;
        }

        if ($total <= 0) {
            return redirect('home')->with('lastAct', 'Total pesanan tidak valid.');
        }

        $pendingOrder = DB::table('order')
            ->where('user_id', Auth::id())
            ->where('payment-status', 'pending')
            ->latest()
            ->first();

        if ($pendingOrder) {
            $orderId = (int) $pendingOrder->id;

            DB::table('order')->where('id', $orderId)->update([
                'total' => $total,
                'customer' => $csName,
                'customer_id' => $customerId,
                'loyalty_points_earned' => null,
                'updated_at' => now(),
            ]);

            DB::table('ordered_items')->where('order_id', $orderId)->delete();
        } else {
            $orderId = (int) DB::table('order')->insertGetId([
                'total' => $total,
                'customer' => $csName,
                'customer_id' => $customerId,
                'status' => 'waiting-payment',
                'payment-status' => 'pending',
                'user_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($baskets as $basket) {
            DB::table('ordered_items')->insert([
                'order_id' => $orderId,
                'menu_id' => $basket->menu_id,
                'user_id' => Auth::id(),
                'quantity' => $basket->quantity,
                'variant' => $basket->variant ?? '-',
                'size' => $basket->size ?? '-',
                'ice' => $basket->ice ?? '-',
                'sugar' => $basket->sugar ?? '-',
                'subtotal' => $basket->subtotal,
                'status' => 'waiting-payment',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (! $this->snapTokenService->configured()) {
            Log::error('Midtrans is not configured.');

            return redirect('home')->with(
                'lastAct',
                'Pembayaran Midtrans belum dikonfigurasi. Set MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY di environment.'
            );
        }

        try {
            $snapToken = $this->snapTokenService->create($orderId, $total, $baskets, $csName);
        } catch (\Throwable $e) {
            return redirect('home')->with(
                'lastAct',
                'Gagal membuat sesi pembayaran Midtrans. Coba lagi atau gunakan pembayaran tunai.'
            );
        }

        return view('order', [
            'baskets' => $baskets,
            'csName' => $csName,
            'snapToken' => $snapToken,
            'successUrl' => route('payment-success'),
            'orderTarget' => $orderId,
            'total' => $total,
            'memberCustomer' => $customerId ? Customer::query()->find($customerId) : null,
        ]);
    }

    public function paymentSuccess(Request $request)
    {
        $orderId = $request->input('cartToDelete');
        $order = DB::table('order')->where('id', $orderId)->first();

        if (! $order) {
            return redirect('home')->with('lastAct', 'Pesanan tidak ditemukan.');
        }

        $payReference = $request->input('order_id', $order->payReference ?? '-');
        $total = (int) ($order->total ?? 0);
        $pay = (int) ($order->amountPaid ?? $total);
        $change = 0;

        $this->clearUserCart();

        if ($order->{'payment-status'} !== 'success') {
            $this->finalizeOrderPayment($orderId, [
                'status' => 'paid',
                'amountPaid' => $total,
                'amountChange' => $change,
                'payment-status' => 'success',
                'payReference' => $payReference,
            ], 'QRIS');
            $this->recordPayment($orderId, $total, 'QRIS', $payReference);
            $pay = $total;
        }

        return $this->receiptView(
            $orderId,
            $request->input('customerName', $order->customer),
            $pay,
            $change,
            'QRIS',
            $payReference
        );
    }

    private function getOrderItems(int|string $orderId)
    {
        $baskets = DB::table('ordered_items')
            ->where('order_id', $orderId)
            ->join('menus', 'ordered_items.menu_id', '=', 'menus.id')
            ->select(
                'ordered_items.id',
                'menus.name',
                'menus.price',
                'ordered_items.quantity',
                'ordered_items.variant',
                'ordered_items.size',
                'ordered_items.ice',
                'ordered_items.sugar',
                'ordered_items.subtotal'
            )
            ->get();

        if ($baskets->isEmpty()) {
            $basketOwner = DB::table('carts')->where('user_id', Auth::id())->latest()->first();
            if ($basketOwner) {
                $baskets = DB::table('cart_items')
                    ->where('cart_id', $basketOwner->id)
                    ->join('menus', 'cart_items.menu_id', '=', 'menus.id')
                    ->select(
                        'cart_items.id',
                        'menus.name',
                        'menus.price',
                        'cart_items.quantity',
                        'cart_items.variant',
                        'cart_items.size',
                        'cart_items.ice',
                        'cart_items.sugar',
                        'cart_items.subtotal'
                    )
                    ->get();
            }
        }

        return $baskets->map(function ($item) {
            $item->subtotal = $item->subtotal ?? ($item->price * $item->quantity);

            return $item;
        });
    }

    private function clearUserCart(): void
    {
        $basketOwner = DB::table('carts')->where('user_id', Auth::id())->latest()->first();
        if (! $basketOwner) {
            return;
        }

        DB::table('cart_items')->where('cart_id', $basketOwner->id)->delete();
        DB::table('carts')->where('id', $basketOwner->id)->delete();
    }

    private function finalizeOrderPayment(int|string $orderId, array $fields, ?string $paymentMethod = null): void
    {
        DB::table('order')
            ->where('id', $orderId)
            ->update(array_merge($fields, ['updated_at' => now()]));

        DB::table('ordered_items')
            ->where('order_id', $orderId)
            ->update(['status' => 'Ordered', 'updated_at' => now()]);

        $this->customerMembership->awardPointsForOrder($orderId);

        if ($paymentMethod !== null) {
            $this->inventoryOrderPush->pushPaidOrder($orderId, $paymentMethod);
        }
    }

    private function recordPayment(int|string $orderId, int $totalPay, string $method, string $reference): void
    {
        if (DB::table('payment')->where('order_id', $orderId)->exists()) {
            return;
        }

        DB::table('payment')->insert([
            'order_id' => $orderId,
            'totalPay' => $totalPay,
            'method' => $method,
            'status' => 'success',
            'reference' => $reference,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function generateCashTransactionReference(): string
    {
        return 'KAYU-TRXCASH-'.random_int(100000, 999999).'-'.date('Ymd');
    }

    private function receiptView(
        int|string $orderId,
        ?string $csName,
        int $pay,
        int $change,
        string $paymentMethod,
        string $payReference = '-',
        ?object $order = null,
    ) {
        $baskets = $this->getOrderItems($orderId);
        $total = (int) $baskets->sum('subtotal');
        $order = $order ?? DB::table('order')->where('id', $orderId)->first();
        $loyaltyEarned = (int) ($order->loyalty_points_earned ?? 0);
        $memberPhone = null;
        $memberTotalPoints = null;

        if (! empty($order->customer_id)) {
            $member = Customer::query()->find($order->customer_id);
            if ($member) {
                $memberPhone = CustomerPhone::display($member->phone);
                $memberTotalPoints = (int) $member->loyalty_points;
            }
        }

        return view('reciept', [
            'baskets' => $baskets,
            'csName' => $csName ?? 'Pelanggan',
            'pay' => $pay,
            'change' => $change,
            'total' => $total,
            'orderId' => $orderId,
            'paymentMethod' => $paymentMethod,
            'payReference' => $payReference,
            'orderAt' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            'loyaltyEarned' => $loyaltyEarned,
            'memberPhone' => $memberPhone,
            'memberTotalPoints' => $memberTotalPoints,
        ]);
    }
}
