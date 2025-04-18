<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {  
        // $a=[];
        // $idRef=[];
        // $bucket=[];
        $boards = DB::table('expense_boards')
            ->where('userOwner', Auth::user()->id)
            ->select('id','boardName', 'urgency','created_at','boardCur')
            ->get();

        // foreach($boards as $board){
        //     $id = $board->id;
        //     $items = DB::table('expense_items')
        //         ->where('boardOwner', $id)
        //         ->select('itemPrice')
        //         ->get();
        //         // array_push($idRef, $id);

        // }

        // foreach ($board->id as $key => $email) {
        //     $email->test = "test";
        //  }
        //  return $user->emails;

                
        // foreach($items as $result){
        //             array_push($a, $result);
        // }

        // // foreach ($user->emails as $key => $email) {
        // //     $email->test = "test";
        // //  }
        // //  return $user->emails;

        // // foreach($a as $sums){
        // //     $boards = collect(['id' => 1]);
        // //     $boards->put('priceSums', $sums);
        // //     // array_push($idRef, $sums);
        // // }

        // //         // if($items == " "){
        // //         //     array_push($a, "yes");

        // //         // }else{
        // //         //     array_push($a, $items);
        // //         // }
                    
            
        // // }
        
        // // foreach($items as $result){
        // //     array_push($a, $result);
        // //     // array_push($idRef, $id);
        // // }
        
        
        // // // foreach($boards as $board){
            
        // // // }
        // // // foreach($a as $b){
        // // //     foreach($b as $result){
        // // //         // $boards->put('priceSums', $priceSums);
        // // //         if($result == null){
        // // //             array_push($bucket, " ");

        // // //         }else{
        // // //             array_push($bucket, $result);
                    
        // // //         }
        // // //     }
        // // // }

        // // // foreach($a as $result){
        // // //     // $priceSums = collect($result)->sum('itemPrice');
        // // //     $priceSums = $items->sum('itemPrice');
        // // //     $boards->put('priceSums', $priceSums);
        // // // }
            

        return view('home', ['boards' => $boards]);
        // return view(dd($boards, $a));
    }
}
