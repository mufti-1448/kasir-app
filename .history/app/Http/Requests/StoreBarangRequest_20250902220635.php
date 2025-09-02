<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Kembalikan true untuk mengizinkan request ini.
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255|unique:barangs,nama,NULL,id,deleted_at,NULL',
            'kategori' => 'nullable|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'nullable|string|max:50',
        ];
    }
}
