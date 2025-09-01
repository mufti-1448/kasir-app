@extends('layouts.app')

@section('title', 'Detail Barang: ' . $barang->nama)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Barang: {{ $barang->nama }}</h5>
                    <div>
                        <a href="{{ route('barangs.edit', $barang) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('barangs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Nama Barang</th>
                                    <td>{{ $barang->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>{{ $barang->kategori ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Harga Beli</th>
                                    <td>Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Harga Jual</th>
                                    <td>Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Stok</th>
                                    <td>{{ $barang->stok }}</td>
                                </tr>
                                <tr>
                                    <th>Satuan</th>
                                    <td>{{ $barang->satuan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
