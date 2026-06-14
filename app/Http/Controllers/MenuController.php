<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\cartItems;
use App\Services\Inventory\InventoryMenuSyncService;
use App\Support\MenuCatalog;
use App\Support\MenuOptions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MenuController extends Controller
{
    public function __construct(
        private InventoryMenuSyncService $inventoryMenuSync,
    ) {}

    public function index()
    {
        $this->inventoryMenuSync->ensureSynced();

        $menuCatalog = MenuCatalog::build(
            $this->visibleMenusQuery()
                ->select('name', 'description', 'category', 'price', 'most_ordered', 'img_url', 'inventory_menu_code')
                ->get()
        );

        return view('welcome', compact('menuCatalog'));
    }

    public function orderPage()
    {
        $this->inventoryMenuSync->ensureSynced();

        $menuCatalog = MenuCatalog::build(
            $this->visibleMenusQuery()
                ->select('id', 'name', 'description', 'category', 'price', 'most_ordered', 'img_url', 'inventory_menu_code', 'options')
                ->get(),
            withOptions: true
        );

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
            'menuCatalog' => $menuCatalog,
        ]);

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
        $cartItemId = (int) $request->input('update-target');

        DB::table('cart_items')
            ->where('id', $cartItemId)
            ->decrement('quantity');

        $item = DB::table('cart_items')
            ->join('menus', 'cart_items.menu_id', '=', 'menus.id')
            ->where('cart_items.id', $cartItemId)
            ->select('cart_items.quantity', 'menus.price')
            ->first();

        if ($item) {
            $this->refreshCartItemSubtotal($cartItemId, (int) $item->price);
        }

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

            $this->refreshCartItemSubtotal((int) $cartItem->id, (int) $menu->price);
        } else {
            $cartItemId = DB::table('cart_items')->insertGetId([
                'cart_id' => $basketOwner->id,
                'menu_id' => $menuId,
                'variant' => $variant,
                'size' => $size,
                'ice' => $ice,
                'sugar' => $sugar,
                'quantity' => 1,
                'subtotal' => (int) $menu->price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->refreshCartItemSubtotal($cartItemId, (int) $menu->price);
        }

        return redirect('home')->with('lastAct', 'Pesanan ditambahkan ke keranjang.');
    }

    private function visibleMenusQuery()
    {
        $query = DB::table('menus')->where('is_active', true);

        if (config('inventory.enabled')) {
            $query->whereNotNull('inventory_menu_code');
        }

        return $query;
    }

    private function refreshCartItemSubtotal(int $cartItemId, int $menuPrice): void
    {
        $quantity = (int) DB::table('cart_items')->where('id', $cartItemId)->value('quantity');

        DB::table('cart_items')
            ->where('id', $cartItemId)
            ->update([
                'subtotal' => max(0, $quantity) * $menuPrice,
                'updated_at' => now(),
            ]);
    }
}

// cartItems
// itemable_type, quantity, price, subtotal, variant, size, ice, sugar, options, created_at, updated_at

// menus
// id, name, description, category, price, most_ordered, img_url