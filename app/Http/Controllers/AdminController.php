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


class AdminController extends Controller
{

    // Menu management -------------------------------------------------
    public function indexMenu()
    {
        $menuItems = DB::table('menus')
            ->select('id', 'name', 'description', 'category', 'price', 'most_ordered', 'img_url')
            ->get();
            
        return view('management/menu', ['menuItems' => $menuItems]);
    }

    public function detailMenu(Request $request)
    {
        $menuItems = DB::table('menus')
            ->where('id', $request->route('id'))
            ->select('id', 'name', 'description', 'category', 'price', 'most_ordered', 'img_url')
            ->get();
            
        return view('management/menuDetail', ['menuItems' => $menuItems]);

        
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
                        ->update([
                            'name' => $request->name,
                            'description' => $request->description,
                            'category' => str_replace([" / Click to change"], '', $request->category),
                            'price' => str_replace(['+', '-'], '', filter_var($request->price, FILTER_SANITIZE_NUMBER_INT)),
                            'most_ordered' => $request->has('most_ordered'),
                            'img_url' => $path
                        ]);
                    return redirect()->route('menuData');
                    // return dd($request, $path);
        }else{

        Menu::where('id', $request->route('id'))
                        ->update([
                            'name' => $request->name,
                            'description' => $request->description,
                            'category' => str_replace([" / Click to change"], '', $request->category),
                            'price' => str_replace(['+', '-'], '', filter_var($request->price, FILTER_SANITIZE_NUMBER_INT)),
                            'most_ordered' => $request->has('most_ordered')
                        ]);
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
            
            
        Menu::create([
                        'name' => $request->name,
                        'description' => $request->description,
                        'category' => $request->category,
                        'price' => str_replace(['+', '-'], '', filter_var($request->price, FILTER_SANITIZE_NUMBER_INT)),
                        'most_ordered' => $request->has('most_ordered'),
                        'img_url' => $path
                    ]);
                    return redirect()->route('menuData');
                    // return dd($request, $path);
        }else{

        Menu::create([
                        'name' => $request->name,
                        'description' => $request->description,
                        'category' => $request->category,
                        'price' => str_replace(['+', '-'], '', filter_var($request->price, FILTER_SANITIZE_NUMBER_INT)),
                        'most_ordered' => $request->has('most_ordered'),
                        'img_url' => 'img/item_placeholder.png'
                    ]);
                    return redirect()->route('menuData');
                    // return dd($request);

        }

        
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
                        'password' => $password = Hash::make('new'.$request->phone)
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
    
    public function detailOrder()
    {
        $cashiers = DB::table('order')
            ->join('users', 'user_id', '=', 'users.id')
            ->select('total', 'amountPaid', 'amountChange', 'payReference')
            ->get();

        $cashierItems = DB::table('ordered_items')
            ->join('menus', 'menu_id', '=', 'menus.id')
            ->join('users', 'user_id', '=', 'users.id')
            ->select('ordered_items.id', 'order_id', 'menus.name', 'users.name', 'quantity', 'variant', 'size', 'ice', 'sugar', 'subtotal', 'status')
            ->get();

        // return dd( $cashierItems);
        return view('management/orderDetail', ['cashierItems' => $cashierItems] );
    }

    public function exportOrder() 
    {
        return Excel::download(new OrderExport(), 'order.xlsx');
        // return (new OrderExport)->download('invoices.xlsx');
    }

    
    public function indexPayment()
    {

        $payments = DB::table('payment')
            // ->join('users', 'user_id', '=', 'users.id')
            ->select('id', 'order_id', 'totalPay', 'method', 'status', 'reference')
            ->get();

        // return dd($cashier, $cashierItems);
        return view('management/payment', ['payments' => $payments] );
    }
    
    // Menu management -------------------------------------------------

    public function exportPayment() 
    {
        return Excel::download(new PaymentExport(), 'payment.xlsx');
        // return (new OrderExport)->download('invoices.xlsx');
    }
}

// str_replace(['+', '-'], '', filter_var($request->input("price_".$p), FILTER_SANITIZE_NUMBER_INT)
