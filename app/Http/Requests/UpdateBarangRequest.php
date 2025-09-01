<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Dapatkan ID barang yang sedang diupdate dari route parameter
        $barangId = $this->route('barang')->id;

        return [
            // Ignore rule unique untuk nama barang yang sedang diupdate
            'nama' => 'required|string|max:255|unique:barangs,nama,' . $barangId,
            'kategori' => 'nullable|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'nullable|string|max:50',
        ];
    }
}
