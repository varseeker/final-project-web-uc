<?php

namespace App\Http\Controllers;

use App\Models\expenseBoards;
use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\Rekening;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Mendapatkan data pengguna yang sedang login

        if ($user->role === 'admin') {
            // Logika untuk admin
            $totalSaldo = Rekening::sum('saldo');
            $rekeningAktif = Rekening::count();
            $transaksiTerbaru = Transaksi::orderBy('created_at', 'desc')->limit(5)->get();
            $nasabahBaru = Rekening::where('created_at', '>=', Carbon::now()->subDays(7))->count();

            $transaksiBulananLabels = ['Januari', 'Februari', 'Maret', 'April']; // Contoh data
            $transaksiBulananData = [10, 20, 30, 40]; // Contoh data
            $saldoRekeningLabels = ['Tabungan', 'Giro', 'Deposito'];
            $saldoRekeningData = [50000, 30000, 20000];

            return view('home', compact(
                'totalSaldo',
                'rekeningAktif',
                'transaksiTerbaru',
                'nasabahBaru',
                'transaksiBulananLabels',
                'transaksiBulananData',
                'saldoRekeningLabels',
                'saldoRekeningData'
            ));
        } elseif ($user->role === 'nasabah') {
            // Logika untuk nasabah
            // Ambil data produk yang dimiliki oleh nasabah berdasarkan `id_nasabah`
            $produkNasabah = DB::table('rekening')
            ->join('produk', 'rekening.id_produk', '=', 'produk.id_produk')
            ->where('rekening.id_nasabah', $user->id_nasabah)
            ->select('rekening.nomor_rekening', 'rekening.saldo', 'produk.nama')
            ->get();
            
            // Ambil daftar produk untuk dropdown Tambah Rekening
            $produkList = DB::table('produk')->get();
            $historyTrx = Transaksi::with('rekening')->latest()->get();

            return view('home', compact('produkNasabah', 'produkList'));
        }

        // Jika role tidak dikenali, kembalikan ke halaman error atau redirect
        return redirect()->route('home')->withErrors(['message' => 'Role tidak dikenali.']);
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editCurrentUser($id)
    {
        $user = Nasabah::with('user')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return view('profile', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCurrentUser(Request $request, $id)
    {
        $user = Nasabah::with('user')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = $request->validate([
            'status_pekerjaan' => ['sometimes', 'string', 'max:255'],
            'noTelepon' => ['sometimes', 'string', 'max:15'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->user_id],
        ]);

        \DB::beginTransaction();

        try {
            $user->update([
                'status_pekerjaan' => $data['status_pekerjaan'] ?? $user->status_pekerjaan,
                'nomor_telepon' => $data['noTelepon'] ?? $user->nomor_telepon,
                'email' => $data['email'] ?? $user->email,
            ]);

            $user->user->update([
                'email' => $data['email'] ?? $user->user->email,
            ]);

            \DB::commit();

            return redirect()->route('dashboard')->with('success', 'Berhasil Update data Profile.');

        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Update Error: ' . $e->getMessage());

            return response()->json(['message' => $e], 500);
        }
    }
    
    public function getHistoryByRekening(Request $request)
    {
        // Ambil nomor rekening yang dikirimkan
        $rekening = $request->input('nomor_rekening');

        // Ambil data transaksi yang sesuai dengan nomor rekening
        $transactions = Transaksi::where('nomor_rekening_asal', $rekening)
            ->orWhere('nomor_rekening_tujuan', $rekening)
            ->with('rekening')
            ->latest()
            ->get();

        // Kembalikan response JSON untuk data transaksi
        return response()->json($transactions);
    }
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $boards = DB::table('expense_boards')
    //         ->where('id', $request->route('id'))
    //         ->select('id', 'boardName', 'urgency','boardCur')
    //         ->get();
    //     $items = DB::table('expense_items')
    //         ->where('boardOwner', $request->route('id'))
    //         ->select('id', 'itemName', 'itemDesc','itemPrice','status')
    //         ->get();

    //     return view('boards/board', ['boards' => $boards, 'items' => $items]);
    // }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
        
    //     date_default_timezone_set('Asia/Jakarta');
        
    //     if($request->input("board-name") == ' '){
    //         $boardNameVar = ' ';
    //     }else{
    //         $boardNameVar = $request->input("board-name");
    //     }
        


    //     DB::insert('insert into expense_boards (id, userOwner, boardName, boardCur, urgency, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?)', [NULL, Auth::user()->id, $boardNameVar, 'IDR', 'normal', now(), now()]);
    //     return redirect()->route('home');
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(expenseBoards $expenseBoards)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(expenseBoards $expenseBoards)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, expenseBoards $expenseBoards)
    // {
    //     expenseBoards::where($request->route('id'))
    //                 ->update([
    //                     'boardName' => $request->board_name
    //                 ]);
    //                 return redirect()->route('home');
                  
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Request $request, expenseBoards $expenseBoards)
    // {
    //     expenseBoards::destroy($request->input("board-target"));
    //     // return view(dd($request->input("board-target")));
    //     return redirect()->route('home');
    
    // }
}
