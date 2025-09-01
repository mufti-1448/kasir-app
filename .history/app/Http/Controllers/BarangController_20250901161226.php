<?php

namespace App\Http\Controllers;

// PERBAIKAN 1: Import statement yang benar dan lengkap
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Ambil data dari request (search & kategori)
        $search = $request->input('search');
        $kategori = $request->input('kategori');

        // 2. Mulai query Eloquent
        $barangs = Barang::query()
            ->orderBy('nama', 'asc'); // Urutkan A-Z

        // 3. Terapkan Filter Pencarian (jika ada)
        if ($search) {
            $barangs->where(function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        // 4. Terapkan Filter Kategori (jika ada dan bukan "semua")
        if ($kategori && $kategori != 'semua') {
            $barangs->where('kategori', $kategori);
        }

        // 5. Eksekusi query dengan PAGINATION (15 item per halaman)
        $barangs = $barangs->paginate(15);

        // 6. Ambil daftar kategori unik untuk dropdown filter
        $kategoris = Barang::distinct()->whereNotNull('kategori')->pluck('kategori');

        // 7. Kirim data ke View
        return view('barangs.index', compact('barangs', 'search', 'kategori', 'kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil daftar kategori unik untuk dropdown (jika ada)
        $kategoris = Barang::distinct()->whereNotNull('kategori')->pluck('kategori');
        return view('barangs.create', compact('kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBarangRequest $request) // PERBAIKAN 4: Ganti Request umum dengan StoreBarangRequest
    {
        // Validasi data sudah otomatis dilakukan oleh StoreBarangRequest
        $validated = $request->validated();

        // Buat record baru di database
        Barang::create($validated);

        // Redirect ke halaman daftar barang + pesan sukses
        return redirect()->route('barangs.index')
            ->with('success', 'Barang berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang) // PERBAIKAN: Gunakan Route Model Binding (langsung dapatkan object Barang)
    {
        // Jika ingin membuat halaman detail barang
        return view('barangs.show', compact('barang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang) // PERBAIKAN: Gunakan Route Model Binding
    {
        $kategoris = Barang::distinct()->whereNotNull('kategori')->pluck('kategori');
        return view('barangs.edit', compact('barang', 'kategoris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBarangRequest $request, Barang $barang) // PERBAIKAN 4: Ganti Request umum dengan UpdateBarangRequest
    {
        // Validasi data sudah otomatis dilakukan oleh UpdateBarangRequest
        $validated = $request->validated();

        // Update data barang
        $barang->update($validated);

        return redirect()->route('barangs.index')
            ->with('success', 'Data barang berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang) // PERBAIKAN: Gunakan Route Model Binding
    {
        // Hapus data (soft delete)
        $barang->delete();

        return redirect()->route('barangs.index')
            ->with('success', 'Barang berhasil dihapus!');
    }
}
