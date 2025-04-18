<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Produk;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RekeningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rekenings = DB::table('rekening')
        ->join('produk', 'rekening.id_produk', '=', 'produk.id_produk')
        ->join('nasabah', 'rekening.id_nasabah', '=', 'nasabah.id_nasabah')
        ->select('rekening.nomor_rekening', 'rekening.saldo', 'produk.nama', 'produk.jenis', 'nasabah.id_nasabah', 'nasabah.nama', 'rekening.created_at')
        ->get();
        return view('rekening.index', compact('rekenings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nasabahs = Nasabah::all();
        return view('rekening.create', compact('nasabahs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['saldo'] = str_replace(',', '', $data['saldo']); // Remove commas for validation
        
            $request->merge(['saldo' => $data['saldo']]);
        
            $request->validate([
                'id_nasabah' => 'required',
                'jenis_rekening' => 'required',
                'saldo' => 'required|numeric',
                'tanggal_pembukaan' => 'required|date',
            ]);
            
            Rekening::create($data);
            return redirect()->route('rekening.index')->with('success', 'Rekening created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()])->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Rekening $rekening)
    {
        
        $rekening_selected = DB::table('rekening')
        ->join('produk', 'rekening.id_produk', '=', 'produk.id_produk')
        ->join('nasabah', 'rekening.id_nasabah', '=', 'nasabah.id_nasabah')
        ->where('rekening.nomor_rekening', $rekening->nomor_rekening)
        ->select('nasabah.nama as nama_nasabah', 'nasabah.id_nasabah', 'rekening.nomor_rekening', 'rekening.saldo', 'produk.nama', 'produk.jenis', 'produk.deskripsi', 'rekening.created_at')
        ->first();
        return view('rekening.show', compact('rekening_selected'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rekening $rekening)
    {
        $nasabahs = Nasabah::all();
        return view('rekening.edit', compact('rekening', 'nasabahs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rekening $rekening)
    {
        $request->validate([
            'id_nasabah' => 'required',
            'jenis_rekening' => 'required',
            'saldo' => 'required|numeric',
            'tanggal_pembukaan' => 'required|date',
        ]);

        $data = $request->all();
        $data['saldo'] = str_replace(',', '', $data['saldo']); // Remove commas for database storage

        $rekening->update($data);
        return redirect()->route('rekening.index')->with('success', 'Rekening updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rekening $rekening)
    {
        $rekening->delete();
        return redirect()->route('rekening.index')->with('success', 'Rekening deleted successfully.');
    }
    
    public function addRekening(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'saldo_awal' => 'required|numeric|min:0',
        ]);
        $user = Auth::user();
        $produk = Produk::find($request->id_produk);

        $biayaAdmin = $produk->biaya_admin;
        if ($request->saldo_awal < $biayaAdmin) {
            return redirect()->back()->with('error', 'Saldo awal tidak cukup untuk dikurangi biaya admin.');
        }
        $saldoAkhir = $request->saldo_awal - $biayaAdmin;
        if ($produk->minimum_saldo > $saldoAkhir) {
            return redirect()->back()->with('error', 'Saldo awal harus lebih besar dari saldo minimum produk setelah di kurangi biaya admin.');
        } else {
            // Generate nomor rekening (contoh: random 10 digit)
            $nomorRekening = mt_rand(1000000000, 9999999999);
        
            // Simpan rekening baru
            Rekening::create([
                'id_nasabah' => $user->id_nasabah, // id_nasabah diambil dari user login
                'id_produk' => $request->id_produk,
                'nomor_rekening' => $nomorRekening,
                'saldo' => $saldoAkhir,
            ]);
        
            return redirect()->back()->with('success', 'Rekening berhasil ditambahkan.');
        }
    }    

    public function cekRekening($id)
    {
        $rekening = Rekening::find($id);
        if (!$rekening) {
            return response()->json(['message' => 'Rekening tidak ditemukan'], 200);
        }
        return response()->json($rekening, 200);
    }
}
