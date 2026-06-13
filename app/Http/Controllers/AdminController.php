<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\User;
use App\Exports\OrderExport;
use App\Exports\PaymentExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\MenuOptions;


class AdminController extends Controller
{

    // Menu management -------------------------------------------------
    public function indexMenu()
    {
        $menuItems = DB::table('menus')
            ->select('id', 'name', 'description', 'category', 'price', 'most_ordered', 'img_url', 'options')
            ->orderByDesc('id')
            ->get();
            
        return view('management/menu', ['menuItems' => $menuItems]);
    }

    public function detailMenu(Request $request)
    {
        $item = DB::table('menus')
            ->where('id', $request->route('id'))
            ->select('id', 'name', 'description', 'category', 'price', 'most_ordered', 'img_url', 'options')
            ->first();

        if (! $item) {
            abort(404);
        }

        return view('management/menuDetail', [
            'menuItems' => collect([$item]),
            'optionsJson' => $item->options,
            'category' => $item->category,
        ]);

        
    }


    // name="name"
    // name="description"
    // name="price"
    // name="category"
    // name="most_ordered"
    // nama="gambar"

    public function updateMenu(Request $request, Menu $Menu){
            // Validasi
        $request->validate([
            'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // maksimal 2MB
        ]);

        if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
            
            $file = $request->file('gambar');
            $path = 'img/menuImg/'. $file->getClientOriginalName();
                    // return dd($request, $path);
		    $file->move('img/menuImg',$file->getClientOriginalName());
            
            
        Menu::where('id', $request->route('id'))
                        ->update(array_merge([
                            'name' => $request->name,
                            'description' => $request->description,
                            'category' => $this->normalizeMenuCategory($request->category),
                            'price' => str_replace(['+', '-'], '', filter_var($request->price, FILTER_SANITIZE_NUMBER_INT)),
                            'most_ordered' => $request->has('most_ordered'),
                            'img_url' => $path,
                        ], $this->menuOptionsPayload($request)));
                    return redirect()->route('menuData');
                    // return dd($request, $path);
        }else{

        Menu::where('id', $request->route('id'))
                        ->update(array_merge([
                            'name' => $request->name,
                            'description' => $request->description,
                            'category' => $this->normalizeMenuCategory($request->category),
                            'price' => str_replace(['+', '-'], '', filter_var($request->price, FILTER_SANITIZE_NUMBER_INT)),
                            'most_ordered' => $request->has('most_ordered'),
                        ], $this->menuOptionsPayload($request)));
                        return redirect()->route('menuData');
                    // return dd($request);

        }

    }

    public function deleteMenu(Request $request)
    {
        Menu::destroy($request->route('id'));
        return redirect()->route('menuData');
    }

    public function createMenu(Request $request)
    {
        $request->validate([
            'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // maksimal 2MB
        ]);

        if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
            
            $file = $request->file('gambar');
            $path = 'img/menuImg/'. $file->getClientOriginalName();
                    // return dd($request, $path);
		    $file->move('img/menuImg',$file->getClientOriginalName());
            
            
        Menu::create(array_merge([
                        'name' => $request->name,
                        'description' => $request->description,
                        'category' => $this->normalizeMenuCategory($request->category),
                        'price' => str_replace(['+', '-'], '', filter_var($request->price, FILTER_SANITIZE_NUMBER_INT)),
                        'most_ordered' => $request->has('most_ordered'),
                        'img_url' => $path,
                    ], $this->menuOptionsPayload($request)));
                    return redirect()->route('menuData');
                    // return dd($request, $path);
        }else{

        Menu::create(array_merge([
                        'name' => $request->name,
                        'description' => $request->description,
                        'category' => $this->normalizeMenuCategory($request->category),
                        'price' => str_replace(['+', '-'], '', filter_var($request->price, FILTER_SANITIZE_NUMBER_INT)),
                        'most_ordered' => $request->has('most_ordered'),
                        'img_url' => 'img/item_placeholder.png',
                    ], $this->menuOptionsPayload($request)));
                    return redirect()->route('menuData');
                    // return dd($request);

        }

        
    }

    
    private function normalizeMenuCategory(string $category): string
    {
        $category = str_replace([' / Click to change'], '', $category);

        return $category === 'Non-Coffee' ? 'Non-coffee' : $category;
    }

    private function menuOptionsPayload(Request $request): array
    {
        $options = MenuOptions::buildFromAdminRequest($request);

        if ($options === null) {
            $category = $this->normalizeMenuCategory($request->category);

            return ['options' => json_encode(MenuOptions::defaultsForCategory($category))];
        }

        return ['options' => $options];
    }

    // Menu management -------------------------------------------------

    // Crew management -------------------------------------------------

// id
// name
// email
// role
// phone
// address
// password
    public function indexCrew()
    {
        $crews = DB::table('users')
            ->select('id', 'name', 'email', 'role', 'phone', 'address')
            ->get();
            
        // return dd($crews);
        return view('management/crew', ['crews' => $crews]);
    }

    public function detailCrew(Request $request)
    {
        $crews = DB::table('users')
            ->where('id', $request->route('id'))
            ->select('id', 'name', 'email', 'role', 'phone', 'address')
            ->get();
            
        return view('management/crewDetail', ['crews' => $crews]);

        
    }


    // name="name"
    // name="description"
    // name="price"
    // name="category"
    // name="most_ordered"
    // nama="gambar"

    public function updateCrew(Request $request, User $user){

        User::where('id', $request->route('id'))
                        ->update([
                            'name' => $request->name,
                            'email' => $request->email,
                            'role' => str_replace([" / Click to change"], '', $request->role),
                            'phone' => $request->phone,
                            'address' => $request->address,
                            // 'password' => $password = Hash::make('new'.$request->phone)
                        ]);
                        return redirect()->route('crewData');
                    // return dd($request);

        }

    

    public function deleteCrew(Request $request)
    {
        User::destroy($request->route('id'));
        return redirect()->route('crewData');
    }

    public function createCrew(Request $request)
    {
        User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'role' => $request->role,
                        'phone' => $request->phone,
                        'address' => $request->address,
                        'password' => $password = Hash::make($request->name.'123')
                    ]);
                    return redirect()->route('crewData');

        
    }

    
    // Menu management -------------------------------------------------

    // Order & Ordered Item management -------------------------------------------------

    public function indexOrder()
    {

        $cashiers = DB::table('order')
            ->join('users', 'user_id', '=', 'users.id')
            ->select('order.id', 'total', 'amountPaid', 'amountChange', 'customer', 'status', 'payment-status', 'users.name', 'payReference')
            ->get();

        $cashierItems = DB::table('ordered_items')
            ->join('menus', 'menu_id', '=', 'menus.id')
            ->join('users', 'user_id', '=', 'users.id')
            ->select('ordered_items.id', 'order_id', 'menus.name', 'users.name', 'quantity', 'variant', 'size', 'ice', 'sugar', 'subtotal', 'status')
            ->get();

        // return dd($cashier, $cashierItems);
        return view('management/order', ['cashiers' => $cashiers] );
    }
    
    // Menu management -------------------------------------------------
    
    public function detailOrder($id)
    {
        $order = DB::table('order')
            ->join('users', 'order.user_id', '=', 'users.id')
            ->where('order.id', $id)
            ->select(
                'order.id',
                'order.total',
                'order.amountPaid',
                'order.amountChange',
                'order.customer',
                'order.status',
                'order.payment-status',
                'order.payReference',
                'order.created_at',
                'users.name as cashier_name'
            )
            ->first();

        if (! $order) {
            abort(404);
        }

        $orderItems = DB::table('ordered_items')
            ->where('ordered_items.order_id', $id)
            ->join('menus', 'ordered_items.menu_id', '=', 'menus.id')
            ->select(
                'ordered_items.id',
                'ordered_items.order_id',
                'menus.name as menu_name',
                'menus.price as menu_price',
                'ordered_items.quantity',
                'ordered_items.variant',
                'ordered_items.size',
                'ordered_items.ice',
                'ordered_items.sugar',
                'ordered_items.subtotal',
                'ordered_items.status'
            )
            ->get();

        return view('management/orderDetail', [
            'order' => $order,
            'orderItems' => $orderItems,
        ]);
    }

    public function exportOrder() 
    {
        return Excel::download(new OrderExport(), 'order.xlsx');
        // return (new OrderExport)->download('invoices.xlsx');
    }

    
    public function indexPayment()
    {

        $payments = DB::table('payment')
            ->leftJoin('order', 'payment.order_id', '=', 'order.id')
            ->select(
                'payment.id',
                'payment.order_id',
                'payment.totalPay',
                'payment.method',
                'payment.status',
                'payment.reference',
                'order.payReference'
            )
            ->orderByDesc('payment.id')
            ->get();

        // return dd($cashier, $cashierItems);
        return view('management/payment', ['payments' => $payments] );
    }

    public function detailPayment($id)
    {
        $payment = DB::table('payment')
            ->leftJoin('order', 'payment.order_id', '=', 'order.id')
            ->leftJoin('users', 'order.user_id', '=', 'users.id')
            ->where('payment.id', $id)
            ->select(
                'payment.id as payment_id',
                'payment.order_id',
                'payment.totalPay',
                'payment.method',
                'payment.status as payment_status',
                'payment.reference',
                'payment.created_at as paid_at',
                'order.total as order_total',
                'order.amountPaid',
                'order.amountChange',
                'order.customer',
                'order.status as order_status',
                'order.payment-status',
                'order.payReference',
                'users.name as cashier_name'
            )
            ->first();

        if (! $payment) {
            abort(404);
        }

        $trxRef = ($payment->reference && $payment->reference !== '-')
            ? $payment->reference
            : ($payment->payReference ?? '-');

        $orderItems = DB::table('ordered_items')
            ->where('ordered_items.order_id', $payment->order_id)
            ->join('menus', 'ordered_items.menu_id', '=', 'menus.id')
            ->select(
                'ordered_items.id',
                'menus.name as menu_name',
                'menus.price as menu_price',
                'ordered_items.quantity',
                'ordered_items.variant',
                'ordered_items.size',
                'ordered_items.ice',
                'ordered_items.sugar',
                'ordered_items.subtotal',
                'ordered_items.status'
            )
            ->get();

        $itemsTotal = (int) $orderItems->sum('subtotal');
        $orderTotal = (int) ($payment->order_total ?? $itemsTotal);
        $amountPaid = (int) ($payment->amountPaid ?? $payment->totalPay ?? 0);
        $change = (int) ($payment->amountChange ?? max(0, $amountPaid - $orderTotal));

        return view('management/paymentDetail', [
            'payment' => $payment,
            'trxRef' => $trxRef,
            'orderItems' => $orderItems,
            'orderTotal' => $orderTotal,
            'amountPaid' => $amountPaid,
            'change' => $change,
            'itemsTotal' => $itemsTotal,
        ]);
    }
    
    // Menu management -------------------------------------------------

    public function exportPayment() 
    {
        return Excel::download(new PaymentExport(), 'payment.xlsx');
        // return (new OrderExport)->download('invoices.xlsx');
    }
}

// str_replace(['+', '-'], '', filter_var($request->input("price_".$p), FILTER_SANITIZE_NUMBER_INT)
