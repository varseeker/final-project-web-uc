<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\cartItems;
use App\Support\MenuOptions;

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
            ->select('id', 'name', 'description', 'category', 'price', 'most_ordered', 'img_url', 'options')
            ->get()
            ->map(function ($item) {
                $item->option_config = MenuOptions::forMenu($item);

                return $item;
            });

        $groupedItems = $menuItems->groupBy('category');

        $basketOwner = DB::table('carts')
            ->where('user_id', Auth::id())
            ->latest() // ambil yang paling baru
            ->first();

        if ($basketOwner == null) {
            $cartId = DB::table('carts')->insertGetId([
                'user_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $basketOwner = (object) ['id' => $cartId];
        }
            
        // $pass = collect($basketOwner);s

        $baskets = DB::table('cart_items')
            ->where('cart_id', $basketOwner->id)
            ->join('menus', 'menu_id', '=', 'menus.id')
            ->select('cart_items.id', 'cart_id', 'menu_id', 'menus.name', 'menus.description', 'menus.price', 'quantity', 'variant', 'size', 'ice', 'sugar', 'subtotal')
            ->get();

        // $state = 'hello world';

        // return dd($basketOwner, $baskets);;
        $cartCount = (int) $baskets->sum('quantity');

        return view('home', [
            'baskets' => $baskets,
            'cartCount' => $cartCount,
        ], compact('groupedItems'));

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

    public function store(Request $request)
    {
        $menuId = $request->input('menu_id');

        if (! $menuId) {
            foreach ($request->all() as $key => $value) {
                if (str_starts_with($key, 'update_')) {
                    $menuId = $value;
                    break;
                }
            }
        }

        if (! $menuId) {
            return redirect('home')->with('lastAct', 'Gagal menambahkan pesanan.');
        }

        $menu = DB::table('menus')->where('id', $menuId)->first();
        if (! $menu) {
            return redirect('home')->with('lastAct', 'Menu tidak ditemukan.');
        }

        $optionConfig = MenuOptions::forMenu($menu);
        $selections = MenuOptions::selectionsFromRequest($optionConfig, $request, (int) $menuId);
        $variant = $selections['variant'];
        $size = $selections['size'];
        $ice = $selections['ice'];
        $sugar = $selections['sugar'];

        $basketOwner = DB::table('carts')
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        if (! $basketOwner) {
            $cartId = DB::table('carts')->insertGetId([
                'user_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $basketOwner = (object) ['id' => $cartId];
        }

        $cartItem = DB::table('cart_items')
            ->where('cart_id', $basketOwner->id)
            ->where('menu_id', $menuId)
            ->where('variant', $variant)
            ->where('size', $size)
            ->where('ice', $ice)
            ->where('sugar', $sugar)
            ->first();

        if ($cartItem) {
            DB::table('cart_items')
                ->where('id', $cartItem->id)
                ->increment('quantity');
        } else {
            DB::table('cart_items')->insert([
                'cart_id' => $basketOwner->id,
                'menu_id' => $menuId,
                'variant' => $variant,
                'size' => $size,
                'ice' => $ice,
                'sugar' => $sugar,
                'quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect('home')->with('lastAct', 'Pesanan ditambahkan ke keranjang.');
    }
    
}

// cartItems
// itemable_type, quantity, price, subtotal, variant, size, ice, sugar, options, created_at, updated_at

// menus
// id, name, description, category, price, most_ordered, img_url