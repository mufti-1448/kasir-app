<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
            'nama_pembeli' => 'nullable|string|max:255',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'required|exists:barangs,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
            'harga_satuan' => 'required|array|min:1',
            'harga_satuan.*' => 'required|numeric|min:0',
            'bayar' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();

        try {
            // Validasi stok untuk semua barang sebelum memproses
            foreach ($request->barang_id as $k => $barangId) {
                $barang = Barang::findOrFail($barangId);
                $jumlah = $request->jumlah[$k];

                if ($barang->stok < $jumlah) {
                    throw new \Exception("Stok {$barang->nama} tidak cukup. Stok tersedia: {$barang->stok}");
                }
            }

            // Hitung total transaksi
            $total = 0;
            foreach ($request->jumlah as $k => $jml) {
                $total += $jml * $request->harga_satuan[$k];
            }

            // Validasi pembayaran
            if ($request->bayar < $total) {
                throw new \Exception("Jumlah pembayaran kurang dari total transaksi. Total: Rp " . number_format($total));
            }

            $kembali = $request->bayar - $total;

            // Buat transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => 'TRX-' . now()->format('YmdHis') . '-' . Str::random(4),
                'user_id' => auth()->id(),
                'nama_pembeli' => $request->nama_pembeli,
                'total' => $total,
                'bayar' => $request->bayar,
                'kembali' => $kembali,
                'status' => 'selesai',
            ]);

            if (!$transaksi) {
                throw new \Exception('Gagal menyimpan transaksi.');
            }

            // Simpan detail transaksi dan kurangi stok
            foreach ($request->barang_id as $k => $barangId) {
                $barang = Barang::findOrFail($barangId);
                $jumlah = $request->jumlah[$k];
                $hargaSatuan = $request->harga_satuan[$k];
                $subtotal = $jumlah * $hargaSatuan;

                // Simpan detail transaksi
                $transaksi->details()->create([
                    'barang_id' => $barangId,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ]);

                // Kurangi stok barang
                $barang->decrement('stok', $jumlah);
            }

            DB::commit();

            return redirect()->route('transaksis.show', $transaksi)
                ->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaksi error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
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
     * Hapus transaksi
     */
    public function destroy(Transaksi $transaksi)
    {
        DB::beginTransaction();

        try {
            // Kembalikan stok barang
            foreach ($transaksi->details as $detail) {
                $barang = Barang::find($detail->barang_id);
                if ($barang) {
                    $barang->increment('stok', $detail->jumlah);
                }
                $detail->delete();
            }

            $transaksi->delete();

            DB::commit();

            return redirect()->route('transaksis.index')
                ->with('success', 'Transaksi berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Hapus transaksi error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * API untuk validasi stok (opsional, untuk AJAX)
     */
    public function checkStock(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        $barang = Barang::find($request->barang_id);

        return response()->json([
            'available' => $barang->stok >= $request->jumlah,
            'current_stock' => $barang->stok,
            'message' => $barang->stok >= $request->jumlah
                ? 'Stok tersedia'
                : 'Stok tidak cukup. Stok tersedia: ' . $barang->stok
        ]);
    }
}
