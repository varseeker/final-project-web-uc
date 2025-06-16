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
        
        $basketOwner = DB::table('carts')
            ->where('user_id', Auth::id())
            ->latest() // ambil yang paling baru
            ->first();

        $baskets = DB::table('cart_items')
            ->where('cart_id', $basketOwner->id)
            ->join('menus', 'menu_id', '=', 'menus.id')
            ->select('cart_items.id', 'cart_id', 'menu_id', 'menus.name', 'menus.description', 'menus.price', 'quantity', 'variant', 'size', 'ice', 'sugar', 'subtotal')
            ->get();

        DB::table('carts')->where('id', $basketOwner->id)->delete();

        
        
        $pay = str_replace(['+', '-'], '', filter_var($request->cashAmount, FILTER_SANITIZE_NUMBER_INT));
        $wallet = $baskets->map(function ($item) use (&$total) {
            $item->subtotal = $item->price * $item->quantity;

            // total semua item di keranjang
            $total += $item->subtotal;
        });
        $change = $total - $pay ;
        $change = abs($change);
        


        // dd($total, str_replace(['+', '-'], '', filter_var($request->cashAmount, FILTER_SANITIZE_NUMBER_INT)));

        DB::table('order')
            ->where('id', $request->input('cartToDelete'))
            ->where('status', 'waiting-payment')
            ->where('payment-status', 'pending')
            ->update([
                'status'  => 'paid',
                'amountPaid'  => str_replace(['+', '-'], '', filter_var($request->cashAmount, FILTER_SANITIZE_NUMBER_INT)),
                'amountChange'  => $change,
                'payment-status'     => 'success',
                'payReference'     => 'OnCashier_ByCash',
            ]);

        
        DB::table('ordered_items')
            ->where('order_id', $request->input('cartToDelete'))
            ->where('status', 'waiting-payment')
            ->update([
                'status'  => 'Ordered',
            ]);

        DB::table('payment')->insert([
                'order_id' => $request->input('cartToDelete'),
                'totalPay' => str_replace(['+', '-'], '', filter_var($request->cashAmount, FILTER_SANITIZE_NUMBER_INT)),
                'method' => 'Cash',
                'status' => 'success',
                'reference' => '-'
            ]);

        $csName = $request->customerName;
        

        // return dd($snapToken);
        return view('reciept', ['baskets' => $baskets])->with('csName', $csName)->with('pay', $pay);
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
                'order_id' => 'CAUN-TRXMIDTRANS-'.rand().'-'.date("Ymd"),
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

        // try {

        $basketOwner = DB::table('carts')
            ->where('user_id', Auth::id())
            ->latest() // ambil yang paling baru
            ->first();
        // return dd( $basketOwner );

        try {
            DB::table('carts')->where('id', $basketOwner->id)->delete();
        } catch (\Throwable $th) {
            
            return redirect('home');
        }
        DB::table('carts')->where('id', $basketOwner->id)->delete();

        DB::table('order')
            ->where('id', $request->input('cartToDelete'))
            ->where('status', 'waiting-payment')
            ->where('payment-status', 'pending')
            ->update([
                'status'  => 'paid',
                'payment-status'     => 'success',
                'payReference'     => $request->input('order_id'),
            ]);

        
        DB::table('ordered_items')
            ->where('order_id', $request->input('cartToDelete'))
            ->where('status', 'waiting-payment')
            ->update([
                'status'  => 'Ordered',
            ]);

        $money = DB::table('order')
            ->where('id', $request->input('cartToDelete'))
            ->select('total')
            ->get();
            
        DB::table('payment')->insert([
                'order_id' => $request->input('cartToDelete'),
                'totalPay' => $money[0]->total,
                'method' => 'QRIS',
                'status' => 'success',
                'reference' => $request->input('order_id')
            ]);
            
        $basketOwner = DB::table('carts')
            ->where('user_id', Auth::id())
            ->latest() // ambil yang paling baru
            ->first();
        // return dd( $basketOwner );


        // return dd( $request );
        return redirect('home');

    // } catch (\Exception $e) {

    // }
        
        
        // return dd( $request->cartToDelete, $request->order_id );
    }
}
