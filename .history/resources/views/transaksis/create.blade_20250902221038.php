@extends('layouts.app')

@section('title', 'Tambah Transaksi Baru')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tambah Transaksi Baru</h5>
                        <a href="{{ route('transaksis.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form id="form-transaksi" method="POST" action="{{ route('transaksis.store') }}">
                            @csrf

                            <!-- Pilih Barang -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="barang_id">Pilih Barang</label>
                                    <select id="barang_id" class="form-control">
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach ($barangs as $barang)
                                            <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_jual }}"
                                                data-nama="{{ $barang->nama }}">
                                                {{ $barang->nama }} (Stok: {{ $barang->stok }}) - Rp
                                                {{ number_format($barang->harga_jual, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="jumlah">Jumlah</label>
                                    <input type="number" id="jumlah" class="form-control" min="1" value="1">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" id="btn-tambah" class="btn btn-primary w-100">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>

                            <!-- Tabel Transaksi -->
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="tabel-transaksi">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Barang</th>
                                            <th>Harga Satuan</th>
                                            <th>Jumlah</th>
                                            <th>Subtotal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total</th>
                                            <th id="total">Rp 0</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Pembayaran -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="bayar">Bayar</label>
                                    <input type="number" id="bayar" name="bayar" class="form-control" min="0"
                                        value="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="kembali">Kembali</label>
                                    <input type="number" id="kembali" class="form-control" readonly>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Simpan Transaksi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Tambah Barang & Hitung Total -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabelBody = document.querySelector('#tabel-transaksi tbody');
            const totalEl = document.querySelector('#total');
            const bayarEl = document.querySelector('#bayar');
            const kembaliEl = document.querySelector('#kembali');
            let total = 0;

            function updateTotal() {
                total = 0;
                tabelBody.querySelectorAll('tr').forEach(row => {
                    total += parseFloat(row.dataset.subtotal);
                });
                totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
                const bayar = parseFloat(bayarEl.value) || 0;
                kembaliEl.value = (bayar - total).toFixed(0);
            }

            document.querySelector('#btn-tambah').addEventListener('click', function() {
                const select = document.querySelector('#barang_id');
                const jumlah = parseInt(document.querySelector('#jumlah').value);
                if (!select.value || jumlah <= 0) return alert('Pilih barang dan jumlah yang valid');

                const barangId = select.value;
                const nama = select.selectedOptions[0].dataset.nama;
                const harga = parseFloat(select.selectedOptions[0].dataset.harga);
                const subtotal = harga * jumlah;

                const tr = document.createElement('tr');
                tr.dataset.subtotal = subtotal;
                tr.innerHTML = `
            <td>${nama}<input type="hidden" name="barang_id[]" value="${barangId}"></td>
            <td>${harga.toLocaleString('id-ID')}<input type="hidden" name="harga_satuan[]" value="${harga}"></td>
            <td>${jumlah}<input type="hidden" name="jumlah[]" value="${jumlah}"></td>
            <td>${subtotal.toLocaleString('id-ID')}</td>
            <td><button type="button" class="btn btn-sm btn-danger btn-hapus">Hapus</button></td>
        `;
                tabelBody.appendChild(tr);

                tr.querySelector('.btn-hapus').addEventListener('click', function() {
                    tr.remove();
                    updateTotal();
                });

                updateTotal();
            });

            bayarEl.addEventListener('input', updateTotal);
        });
    </script>
@endsection
