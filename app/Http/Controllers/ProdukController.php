<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produk = Produk::paginate(10);
        return view('produk.index', compact('produk'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('produk.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['minimum_saldo'] = str_replace(',', '', $data['minimum_saldo']); // Remove commas for validation
        $data['biaya_admin'] = str_replace(',', '', $data['biaya_admin']);
    
        $request->merge(['minimum_saldo' => $data['minimum_saldo']]);
        $request->merge(['biaya_admin' => $data['biaya_admin']]);
        $request->validate([
            'nama' => 'required',
            'jenis' => 'required',
            'deskripsi' => 'required',
            'suku_bunga' => 'required',
            'minimum_saldo' => 'required|numeric',
            'biaya_admin' => 'required|numeric',
        ]);

        Produk::create($request->all());

        return redirect()->route('produk.index')
                         ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produk $produk)
    {
        return view('produk.show', compact('produk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produk $produk)
    {
        return view('produk.edit', compact('produk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama' => 'required',
            'jenis' => 'required',
            'deskripsi' => 'nullable|string',
            'suku_bunga' => 'required|numeric',
            'minimum_saldo' => 'required|numeric|min:0',
            'biaya_admin' => 'required|numeric|min:0',
        ]);

        $produk->update($request->all());

        return redirect()->route('produk.index')
                         ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produk $produk)
    {
        $produk->delete();

        return redirect()->route('produk.index')
                         ->with('success', 'Produk berhasil dihapus.');
    }
}
