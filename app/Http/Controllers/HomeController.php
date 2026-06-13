<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Dompdf\Options;
use Midtrans\Config;
use Midtrans\Snap;
use Exception;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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

        $cashReference = $this->generateCashTransactionReference();

        $this->clearUserCart();
        $this->finalizeOrderPayment($orderId, [
            'status' => 'paid',
            'amountPaid' => $pay,
            'amountChange' => $change,
            'payment-status' => 'success',
            'payReference' => $cashReference,
        ]);
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

        date_default_timezone_set("Asia/Jakarta"); 
        date("Y/m/d - h:s:i");

        $isRefresh = DB::table('order')
            ->where('user_id', Auth::id())
            ->where('payment-status', 'pending')
            ->latest() // ambil yang paling baru
            ->first();

        $basketOwner = DB::table('carts')
            ->where('user_id', Auth::id())
            ->latest() // ambil yang paling baru
            ->first();

            
        // $baskets = DB::table('cart_items')
        //     ->where('cart_id', $basketOwner->id)
        //     ->join('menus', 'menu_id', '=', 'menus.id')
        //     ->select('cart_items.id', 'cart_id', 'menu_id', 'menus.name', 'menus.description', 'menus.price', 'quantity', 'variant', 'size', 'ice', 'sugar', 'subtotal')
        //     ->get();

        $baskets = DB::table('cart_items')
            ->where('cart_id', $basketOwner->id)
            ->join('menus', 'menu_id', '=', 'menus.id')
            ->select('cart_items.id', 'cart_id', 'menu_id', 'menus.name', 'menus.description', 'menus.price', 'quantity', 'variant', 'size', 'ice', 'sugar', 'subtotal')
            ->get();

        $csName = $request->customerName;
        
        $total = 0;
        $wallet = $baskets->map(function ($item) use (&$total) {
            $item->subtotal = $item->price * $item->quantity;

            // total semua item di keranjang
            $total += $item->subtotal;
        });
        
        

        if ($isRefresh == null) {
            // return dd($total, $baskets);
            // id	total	customer	status	payment-status	user_id
            $orderId = DB::table('order')->insertGetId([
                                'total'  => $total,
                                'customer'  => $csName,
                                'status'  => 'waiting-payment',
                                'payment-status' => 'pending',
                                'user_id' => Auth::id(),
                            ]);
            
            // return dd( $orderId );
            // session()->flash('cartToDelete', $orderId);
                            
            foreach ($baskets as $basket) {     
                DB::table('ordered_items')->insert([
                        'order_id'  => $orderId,
                        'menu_id'  => $basket->menu_id,
                        'user_id'  => Auth::id(),
                        'quantity'  => $basket->quantity,
                        'variant'  => $basket->variant,
                        'size'  => $basket->size,
                        'ice'  => $basket->ice,
                        'sugar'  => $basket->sugar,
                        'subtotal'  => $basket->subtotal,
                        'status'     => 'waiting-payment',
                    ]);
            }      
        }

        $params = [
            'transaction_details' => [
                'order_id' => 'KAYU-TRXMIDTRANS-'.rand().'-'.date("Ymd"),
                'gross_amount' => $total,
            ]
            // 'customer_details' => [
            //     'first_name' => $request->first_name,
            //     'last_name' => $request->last_name,
            //     'email' => $request->email,
            //     'phone' => $request->phone,
            // ],
        ];

        $snapToken = Snap::getSnapToken($params);
        
        // return dd( $snapToken );
        // return response()->json($snapToken);
        $orderTarget = DB::table('order')
            ->where('status', 'waiting-payment')
            ->where('payment-status', 'pending')
            ->where('user_id', Auth::id())
            ->latest() // ambil yang paling baru
            ->first();

        $orderTarget = $orderTarget->id;    
        
        // $money = DB::table('order')
        //     ->where('id', $orderTarget)
        //     ->select('total')
        //     ->get();

        // return dd($money[0]->total);
        // return dd($orderTarget);
        
        // return dd("total belanja ".$total, "Item yg dibeli ".$baskets, "Nama pelanggan ".$csName, "Token ".$snapToken);
        

        return view('order', ['baskets' => $baskets])->with('csName', $csName)->with('snapToken', $snapToken)->with('successUrl', route('payment-success'))->with('orderTarget', $orderTarget);
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
            ]);
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

    private function finalizeOrderPayment(int|string $orderId, array $fields): void
    {
        DB::table('order')
            ->where('id', $orderId)
            ->update(array_merge($fields, ['updated_at' => now()]));

        DB::table('ordered_items')
            ->where('order_id', $orderId)
            ->update(['status' => 'Ordered', 'updated_at' => now()]);
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
        return 'KAYU-TRXCASH-'.rand().'-'.date('Ymd');
    }

    private function receiptView(
        int|string $orderId,
        ?string $csName,
        int $pay,
        int $change,
        string $paymentMethod,
        string $payReference = '-'
    ) {
        $baskets = $this->getOrderItems($orderId);
        $total = (int) $baskets->sum('subtotal');

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
        ]);
    }
}
