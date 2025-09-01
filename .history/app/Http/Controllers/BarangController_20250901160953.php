<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use Illuminate\Http\Request; // Pastikan ini di-import

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

        // 6. Kirim data ke View
        return view('barangs.index', compact('barangs', 'search', 'kategori'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
