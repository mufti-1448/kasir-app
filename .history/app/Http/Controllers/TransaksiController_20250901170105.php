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
        DB::transaction(function () use ($request) {
            $transaksi = Transaksi::create([
                'kode_transaksi' => 'TRX-' . Str::upper(Str::random(6)),
                'user_id' => auth()->id(),
                'total' => 0,
                'bayar' => 0,
                'kembali' => 0,
                'status' => 'selesai',
            ]);

            $total = 0;

            foreach ($request->barang_id as $index => $barangId) {
                $barang = Barang::findOrFail($barangId);
                $jumlah = $request->jumlah[$index];
                $subtotal = $barang->harga_jual * $jumlah;

                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barangId,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $barang->harga_jual,
                    'subtotal' => $subtotal,
                ]);

                // Update stok barang
                $barang->decrement('stok', $jumlah);

                $total += $subtotal;
            }

            $transaksi->update(['total' => $total]);
        });

        return redirect()->route('transaksis.index')
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
