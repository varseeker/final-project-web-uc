<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaksis = Transaksi::with('rekening.nasabah')->paginate(10);
        return view('transaksi.index', compact('transaksis'))
        ->with('i', (request()->input('page', 1) - 1) * 10);;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rekenings = Rekening::with('nasabah')->get();
        return view('transaksi.create', compact('rekenings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['jumlah_transaksi'] = str_replace(',', '', $data['jumlah_transaksi']); // Remove commas for validation
    
        $request->merge(['jumlah_transaksi' => $data['jumlah_transaksi']]);
        $request->validate([
            'nomor_rekening' => 'required|exists:rekening,nomor_rekening',
            'bank_tujuan' => 'required',
            'nomor_rekening_tujuan' => 'required',
            'jenis_transaksi' => 'required',
            'jumlah_transaksi' => 'required|numeric|min:0',
        ]);

        $rekening = Rekening::where('nomor_rekening', $request->nomor_rekening)->first();
        if (bccomp($rekening->saldo, $request->nominal, 2) === -1) {
            return back()->withErrors(['message' => 'Saldo tidak mencukupi untuk melakukan transfer.']);
        }
    
        if ($request->bank_tujuan === 'Banksie') {
            // Validasi nomor rekening tujuan
            $rekeningTujuan = Rekening::where('nomor_rekening', $request->nomor_rekening_tujuan)->first();
        
            // Pastikan rekening asal dan tujuan tidak sama
            if ($rekening->nomor_rekening === $rekeningTujuan->nomor_rekening) {
                return back()->withErrors(['message' => 'Rekening asal dan tujuan tidak boleh sama.']);
            }
    
            if (!$rekeningTujuan) {
                return back()->withErrors(['message' => 'Nomor rekening tujuan tidak ditemukan.']);
            }
    
            // Lakukan transfer
            DB::transaction(function () use ($rekening, $rekeningTujuan, $request) {
                // Kurangi saldo pengirim dengan presisi
                $rekening->saldo = bcsub($rekening->saldo, $request->jumlah_transaksi, 2);
                $rekening->save();

                // Tambah saldo penerima dengan presisi
                $rekeningTujuan->saldo = bcadd($rekeningTujuan->saldo, $request->jumlah_transaksi, 2);
                $rekeningTujuan->save();
    
                // Simpan data transaksi
                Transaksi::create([
                    'nomor_rekening_asal' => $rekening->nomor_rekening,
                    'nomor_rekening_tujuan' => $rekeningTujuan->nomor_rekening,
                    'bank_tujuan' => 'Banksie',
                    'jenis_transaksi' => $request->jenis_transaksi,
                    'jumlah_transaksi' => $request->jumlah_transaksi
                ]);
            });
        } else {
            // Bank lain: hanya menyimpan transaksi tanpa mengurangi/memvalidasi rekening tujuan
            Transaksi::create([
                'nomor_rekening_asal' => $rekening->nomor_rekening,
                'nomor_rekening_tujuan' => $request->nomor_rekening_tujuan,
                'bank_tujuan' => 'Lainnya',
                'jenis_transaksi' => $request->jenis_transaksi,
                'jumlah_transaksi' => $request->jumlah_transaksi
            ]);
            
            // Kurangi saldo pengirim dengan presisi
            $rekening->saldo = bcsub($rekening->saldo, $request->jumlah_transaksi, 2);
            $rekening->save();

        }
        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaksi $transaksi)
    {
        $resultTransaksi = DB::table('transaksi')
        ->join('rekening', 'rekening.nomor_rekening', '=', 'transaksi.nomor_rekening_asal')
        ->join('nasabah', 'nasabah.id_nasabah', '=', 'rekening.id_nasabah')
        ->where('transaksi.id_transaksi', $transaksi->id_transaksi)
        ->select('transaksi.nomor_rekening_asal', 'transaksi.nomor_rekening_tujuan', 'transaksi.bank_tujuan', 'rekening.saldo', 'nasabah.nama', 'transaksi.jumlah_transaksi', 'transaksi.jenis_transaksi', 'transaksi.created_at')
        ->first();
        return view('transaksi.show', compact('resultTransaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaksi $transaksi)
    {
        return view('transaksi.edit', compact('transaksi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'nomor_rekening' => 'required',
            'jenis_transaksi' => 'required',
            'tanggal_transaksi' => 'required',
            'jumlah_transaksi' => 'required|numeric',
        ]);

        $transaksi->update($request->all());
        return redirect()->route('transaksi.index')
                        ->with('success', 'Transaksi updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();

        return redirect()->route('transaksi.index')
                        ->with('success', 'Transaksi deleted successfully');
    }

    public function submitTransfer(Request $request)
    {
        $request->validate([
            'nomor_rekening_asal' => 'required|exists:rekening,nomor_rekening',
            'nomor_rekening_tujuan' => 'required',
            'bank_tujuan' => 'required',
            'nominal' => 'required|numeric|min:0',
        ]);
    
        $rekeningAsal = Rekening::where('nomor_rekening', $request->nomor_rekening_asal)->first();

        // Cek saldo pengirim
        if (bccomp($rekeningAsal->saldo, $request->nominal, 2) === -1) {
            return back()->withErrors(['message' => 'Saldo tidak mencukupi untuk melakukan transfer.']);
        }
    
        if ($request->bank_tujuan === 'Banksie') {
            // Validasi nomor rekening tujuan
            $rekeningTujuan = Rekening::where('nomor_rekening', $request->nomor_rekening_tujuan)->first();
        
            // Pastikan rekening asal dan tujuan tidak sama
            if ($rekeningAsal->nomor_rekening === $rekeningTujuan->nomor_rekening) {
                return back()->withErrors(['message' => 'Rekening asal dan tujuan tidak boleh sama.']);
            }
    
            if (!$rekeningTujuan) {
                return back()->withErrors(['message' => 'Nomor rekening tujuan tidak ditemukan.']);
            }
    
            // Lakukan transfer
            DB::transaction(function () use ($rekeningAsal, $rekeningTujuan, $request) {
                // Kurangi saldo pengirim dengan presisi
                $rekeningAsal->saldo = bcsub($rekeningAsal->saldo, $request->nominal, 2);
                $rekeningAsal->save();

                // Tambah saldo penerima dengan presisi
                $rekeningTujuan->saldo = bcadd($rekeningTujuan->saldo, $request->nominal, 2);
                $rekeningTujuan->save();
    
                // Simpan data transaksi
                Transaksi::create([
                    'jenis_transaksi' => 'Transfer',
                    'nomor_rekening_asal' => $rekeningAsal->nomor_rekening,
                    'nomor_rekening_tujuan' => $rekeningTujuan->nomor_rekening,
                    'bank_tujuan' => 'Banksie',
                    'jumlah_transaksi' => $request->nominal,
                ]);
            });
        } else {
            // Bank lain: hanya menyimpan transaksi tanpa mengurangi/memvalidasi rekening tujuan
            Transaksi::create([
                'jenis_transaksi' => 'Transfer',
                'nomor_rekening_asal' => $rekeningAsal->nomor_rekening,
                'nomor_rekening_tujuan' => $request->nomor_rekening_tujuan,
                'bank_tujuan' => $request->bank_tujuan,
                'jumlah_transaksi' => $request->nominal,
            ]);
            
            // Kurangi saldo pengirim dengan presisi
            $rekeningAsal->saldo = bcsub($rekeningAsal->saldo, $request->nominal, 2);
            $rekeningAsal->save();
        }
    
        return redirect()->back()->with('success', 'Transfer berhasil dilakukan.');

    }
}
