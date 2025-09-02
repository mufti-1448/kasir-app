<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransaksiController extends Controller
{
    /**
     * Tampilkan daftar transaksi
     */
    public function index()
    {
        $transaksis = Transaksi::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('transaksis.index', compact('transaksis'));
    }

    /**
     * Form buat transaksi baru
     */
    public function create()
    {
        $barangs = Barang::where('stok', '>', 0)->get();
        return view('transaksis.create', compact('barangs'));
    }

    /**
     * Simpan transaksi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|array',
            'jumlah' => 'required|array',
            'harga_satuan' => 'required|array',
            'bayar' => 'required|numeric'
        ]);

        $total = 0;
        foreach ($request->jumlah as $k => $jml) {
            $total += $jml * $request->harga_satuan[$k];
        }

        $kembali = $request->bayar - $total;

        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-' . now()->format('YmdHis') . '-' . Str::random(4),
            'user_id' => auth()->id(),
            'total' => $total,
            'bayar' => $request->bayar,
            'kembali' => $kembali,
            'status' => 'selesai',
        ]);


        foreach ($request->barang_id as $k => $barangId) {
            $subtotal = $request->jumlah[$k] * $request->harga_satuan[$k];

            $transaksi->details()->create([
                'barang_id' => $barangId,
                'jumlah' => $request->jumlah[$k],
                'harga_satuan' => $request->harga_satuan[$k],
                'subtotal' => $subtotal,
            ]);

            // kurangi stok barang
            Barang::find($barangId)->decrement('stok', $request->jumlah[$k]);
        }

        return redirect()->route('transaksis.show', ['transaksi' => $transaksi->id])
            ->with('success', 'Transaksi berhasil disimpan!');
    }



    /**
     * Tampilkan detail transaksi
     */
    public function show(Transaksi $transaksi)
    {
        $transaksi->load('details.barang', 'user');
        return view('transaksis.show', compact('transaksi'));
    }

    /**
     * Hapus transaksi (opsional)
     */
    public function destroy(Transaksi $transaksi)
    {
        DB::transaction(function () use ($transaksi) {
            foreach ($transaksi->details as $detail) {
                $detail->barang->increment('stok', $detail->jumlah); // kembalikan stok
                $detail->delete();
            }
            $transaksi->delete();
        });

        return redirect()->route('transaksis.index')->with('success', 'Transaksi berhasil dihapus!');
    }


}
