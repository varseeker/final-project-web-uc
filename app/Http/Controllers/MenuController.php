<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\cartItems;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MenuController extends Controller
{
    public function index()
    {
        $menuItems = DB::table('menus')
            ->select('name', 'description', 'category', 'price', 'most_ordered', 'img_url')
            ->get();
            
        // Group by category manually
        $grouped = collect($menuItems)->groupBy('category');

        return view('welcome', ['menuItems' => $grouped]);
    }

    public function orderPage()
    {

        $menuItems = DB::table('menus')
            ->select('id', 'name', 'description', 'category', 'price', 'most_ordered', 'img_url')
            ->get();

        $groupedItems = collect($menuItems)->groupBy('category');

        $basketOwner = DB::table('carts')
            ->where('user_id', Auth::id())
            ->latest() // ambil yang paling baru
            ->first();

        if ($basketOwner == null) {
            DB::table('carts')->insert(
            array(
                'user_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ));
        
        return redirect('home');
        
        }
            
        // $pass = collect($basketOwner);s

        $baskets = DB::table('cart_items')
            ->where('cart_id', $basketOwner->id)
            ->join('menus', 'menu_id', '=', 'menus.id')
            ->select('cart_items.id', 'cart_id', 'menu_id', 'menus.name', 'menus.description', 'menus.price', 'quantity', 'variant', 'size', 'ice', 'sugar', 'subtotal')
            ->get();

        // $state = 'hello world';

        // return dd($basketOwner, $baskets);;
        return view('home', ['baskets' => $baskets], compact('groupedItems'));

        // return dd($basketOwner, $baskets);;
    }

    public function destroy(Request $request)
    {
        // dd(cartItems::find($request->input("delete-target")));
        cartItems::destroy($request->input("delete-target"));
        // $lastAct = $request->_method;
        return redirect('home')->with('lastAct', 'Pesanan Dihapus');
    }

    public function reduceItems(Request $request)
    {   
            DB::table('cart_items')
                ->where('id', $request->input('update-target'))
                ->decrement('quantity');

        return redirect('home')->with('lastAct', 'Pesanan Dikurangi');
    }

    public function store(Request $request, Menu $menu)
    {
        // dd(cartItems::find($request->input("delete-target")));
        
        $anchor = array_values($request->all());
        $anchor = $anchor[1];

        $basketOwner = DB::table('carts')
            ->where('user_id', Auth::id())
            ->latest() // ambil yang paling baru
            ->first();

        $cartItem = DB::table('cart_items')
            ->where('cart_id', $basketOwner->id)
            ->where('menu_id', $request->input("update_" . $anchor))
            ->where('variant', $request->input("variant-" . $anchor))
            ->where('size', $request->input("size-" . $anchor))
            ->where('ice', $request->input('ice-' . $anchor))
            ->where('sugar', $request->input("sugar-" . $anchor))
            ->first();



        // return dd($cartItem, $request);
        

        if ($cartItem) {
            DB::table('cart_items')
                ->where('id', $cartItem->id)
                ->increment('quantity');

                $stone = DB::table('cart_items')
                    ->where('menu_id', $request->input("update_" . $anchor))
                    ->join('menus', 'menu_id', '=', 'menus.id')
                    ->select('menus.price', 'quantity')
                    ->get();

        } else {
            DB::table('cart_items')->insert([
                'cart_id'  => $basketOwner->id,
                'menu_id'  => $request->input("update_" . $anchor),
                'variant'  => $request->input("variant-" . $anchor),
                'size'     => $request->input("size-" . $anchor),
                'ice'      => $request->input('ice-' . $anchor),
                'sugar'    => $request->input("sugar-" . $anchor),
                'quantity' => 1,
            ]);

            
        }
        
        // return dd($cartItem, $request);
            
        return redirect('home')->with('lastAct', 'Pesanan Ditambahkan');
    }
    
}

// cartItems
// itemable_type, quantity, price, subtotal, variant, size, ice, sugar, options, created_at, updated_at

// menus
// id, name, description, category, price, most_ordered, img_url