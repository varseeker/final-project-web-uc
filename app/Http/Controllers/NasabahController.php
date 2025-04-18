<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;

class NasabahController extends Controller
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
     * Display a listing of the resource.
     */
    public function index()
    {
        $nasabah = Nasabah::paginate(10);
        return view('nasabah.index', compact('nasabah'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('nasabah.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'nomor_telepon' => 'required',
            'email' => 'required|email',
            'tanggal_lahir' => 'required|date',
            'status_pekerjaan' => 'required',
        ]);

        Nasabah::create($request->all());

        return redirect()->route('nasabah.index')
                         ->with('success', 'Nasabah created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Nasabah $nasabah)
    {
        return view('nasabah.show', compact('nasabah'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nasabah $nasabah)
    {
        return view('nasabah.edit', compact('nasabah'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nasabah $nasabah)
    {
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'nomor_telepon' => 'required',
            'email' => 'required|email',
            'tanggal_lahir' => 'required|date',
            'status_pekerjaan' => 'required',
        ]);

        $nasabah->update($request->all());

        return redirect()->route('nasabah.index')
                         ->with('success', 'Nasabah updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nasabah $nasabah)
    {
        $nasabah->delete();

        return redirect()->route('nasabah.index')
                         ->with('success', 'Nasabah deleted successfully');
    }
}
