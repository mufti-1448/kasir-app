<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $q = Barang::query();
        if ($req->filled('search')) {
            $s = $req->search;
            $q->where('nama', 'like', "%{$s}%")
                ->orWhere('kategori', 'like', "%{$s}%");
        }
        if ($req->filled('kategori')) {
            $q->where('kategori', $req->kategori);
        }
        $barangs = $q->paginate(15);
        return view('barang.index', compact('barangs'));
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
