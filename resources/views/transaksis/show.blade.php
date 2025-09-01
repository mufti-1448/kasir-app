@extends('layouts.app')

@section('title', 'Detail Transaksi: ' . $transaksi->kode_transaksi)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Transaksi: {{ $transaksi->kode_transaksi }}</h5>
                    <a href="{{ route('transaksis.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Barang</th>
                                <th>Harga Satuan</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaksi->details as $i => $detail)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $detail->barang->nama }}</td>
                                <td>Rp {{ number_format($detail->harga_satuan,0,',','.') }}</td>
                                <td>{{ $detail->jumlah }}</td>
                                <td>Rp {{ number_format($detail->subtotal,0,',','.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total</th>
                                <th>Rp {{ number_format($transaksi->total,0,',','.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Bayar</th>
                                <th>Rp {{ number_format($transaksi->bayar,0,',','.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Kembali</th>
                                <th>Rp {{ number_format($transaksi->kembali,0,',','.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
