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

                            <!-- Informasi Transaksi & Nama Pembeli -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nama_pembeli">Nama Pembeli</label>
                                        <input type="text" name="nama_pembeli" id="nama_pembeli" class="form-control"
                                            placeholder="Masukkan nama pembeli (opsional)"
                                            value="{{ old('nama_pembeli') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tanggal">Tanggal</label>
                                        <input type="text" class="form-control" value="{{ now()->format('d/m/Y H:i') }}"
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kasir">Kasir</label>
                                        <input type="text" class="form-control" value="{{ auth()->user()->name }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Pilih Barang -->
                            <div class="row mb-3 align-items-end">
                                <div class="col-md-5 d-flex flex-column">
                                    <label for="barang_id">Pilih Barang</label>
                                    <select id="barang_id" class="form-control mb-1">
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach ($barangs as $barang)
                                            <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_jual }}"
                                                data-stok="{{ $barang->stok }}" data-nama="{{ $barang->nama }}">
                                                {{ $barang->kode }} - {{ $barang->nama }} (Stok: {{ $barang->stok }}) - Rp
                                                {{ number_format($barang->harga_jual, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small id="stok-info" style="min-height: 20px;" class="text-muted"></small>
                                </div>

                                <div class="col-md-3 pb-3">
                                    <label for="jumlah">Jumlah</label>
                                    <input type="number" id="jumlah" class="form-control" min="1" value="1">
                                </div>

                                <div class="col-md-4 d-flex pb-1">
                                    <button type="button" id="btn-tambah" class="btn btn-primary w-100">
                                        <i class="fas fa-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>


                            <!-- Tabel Transaksi -->
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-hover" id="tabel-transaksi">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Barang</th>
                                            <th>Harga Satuan</th>
                                            <th>Jumlah</th>
                                            <th>Subtotal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Items akan ditambahkan di sini via JavaScript -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-primary">
                                            <th colspan="3" class="text-end">Total</th>
                                            <th id="total">Rp 0</th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-end">Bayar (Rp)</th>
                                            <th>
                                                <input type="number" name="bayar" id="bayar" class="form-control"
                                                    min="0" required value="{{ old('bayar', 0) }}">
                                            </th>
                                            <th></th>
                                        </tr>
                                        <tr class="table-info">
                                            <th colspan="3" class="text-end">Kembali (Rp)</th>
                                            <th id="kembali">0</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle"></i> Simpan Transaksi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const barangSelect = document.getElementById('barang_id');
            const jumlahInput = document.getElementById('jumlah');
            const btnTambah = document.getElementById('btn-tambah');
            const tabelBody = document.querySelector('#tabel-transaksi tbody');
            const totalEl = document.getElementById('total');
            const bayarEl = document.getElementById('bayar');
            const kembaliEl = document.getElementById('kembali');
            const stokInfo = document.getElementById('stok-info');
            const form = document.getElementById('form-transaksi');

            let items = [];

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            function updateStokInfo() {
                if (!barangSelect.value) {
                    stokInfo.textContent = '';
                    return;
                }
                const selectedOption = barangSelect.options[barangSelect.selectedIndex];
                let stok = parseInt(selectedOption.getAttribute('data-stok'));
                if (stok <= 0) {
                    stokInfo.textContent = 'Stok habis';
                    stokInfo.className = 'text-danger';
                    btnTambah.disabled = true;
                } else {
                    stokInfo.textContent = `Stok tersedia: ${stok}`;
                    stokInfo.className = 'text-muted';
                    btnTambah.disabled = false;
                }
                jumlahInput.setAttribute('max', stok);
                if (parseInt(jumlahInput.value) > stok) jumlahInput.value = stok;
            }

            function updateTotal() {
                let total = items.reduce((sum, item) => sum + item.harga * item.jumlah, 0);
                totalEl.textContent = 'Rp ' + formatRupiah(total);

                const bayar = parseFloat(bayarEl.value) || 0;
                const kembali = bayar - total;
                kembaliEl.textContent = formatRupiah(kembali);

                // Warn jika bayar kurang
                if (bayar < total) {
                    bayarEl.classList.add('is-invalid');
                    kembaliEl.parentElement.classList.add('table-danger');
                } else {
                    bayarEl.classList.remove('is-invalid');
                    kembaliEl.parentElement.classList.remove('table-danger');
                }
            }

            barangSelect.addEventListener('change', updateStokInfo);

            btnTambah.addEventListener('click', function() {
                if (!barangSelect.value) {
                    alert('Pilih barang terlebih dahulu');
                    return;
                }

                const selectedOption = barangSelect.options[barangSelect.selectedIndex];
                const barangId = barangSelect.value;
                const nama = selectedOption.getAttribute('data-nama');
                const kode = selectedOption.getAttribute('data-kode');
                let harga = parseFloat(selectedOption.getAttribute('data-harga'));
                let stok = parseInt(selectedOption.getAttribute('data-stok'));
                let jumlah = parseInt(jumlahInput.value);

                if (jumlah > stok) {
                    alert(`Stok tidak cukup. Stok tersedia: ${stok}`);
                    return;
                }

                // Kurangi stok
                stok -= jumlah;
                selectedOption.setAttribute('data-stok', stok);
                updateStokInfo();

                const existingItem = items.find(item => item.barangId === barangId);

                if (existingItem) {
                    existingItem.jumlah += jumlah;
                    const row = tabelBody.querySelector(`tr[data-barang-id="${barangId}"]`);
                    row.cells[2].textContent = existingItem.jumlah;
                    row.cells[3].textContent = 'Rp ' + formatRupiah(existingItem.jumlah * harga);
                } else {
                    const newItem = {
                        barangId,
                        kode,
                        nama,
                        harga,
                        jumlah
                    };
                    items.push(newItem);

                    const newRow = tabelBody.insertRow();
                    newRow.setAttribute('data-barang-id', barangId);
                    newRow.innerHTML = `
                <td>${kode} - ${nama}</td>
                <td>Rp ${formatRupiah(harga)}</td>
                <td>${jumlah}</td>
                <td>Rp ${formatRupiah(harga * jumlah)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger btn-hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

                    newRow.querySelector('.btn-hapus').addEventListener('click', function() {
                        // Kembalikan stok
                        const selectedOption = barangSelect.querySelector(
                            `option[value="${barangId}"]`);
                        let currentStok = parseInt(selectedOption.getAttribute('data-stok'));
                        currentStok += newItem.jumlah;
                        selectedOption.setAttribute('data-stok', currentStok);
                        items = items.filter(item => item.barangId !== barangId);
                        newRow.remove();
                        updateStokInfo();
                        updateTotal();
                    });
                }

                updateTotal();
                jumlahInput.value = 1;
            });

            bayarEl.addEventListener('input', updateTotal);

            form.addEventListener('submit', function(e) {
                if (items.length === 0) {
                    e.preventDefault();
                    alert('Tambahkan minimal satu item ke keranjang');
                    return;
                }

                // Hapus input hidden lama jika ada
                form.querySelectorAll('input[type="hidden"]').forEach(i => i.remove());

                // Tambahkan input hidden untuk setiap item
                items.forEach((item, index) => {
                    const inputBarang = document.createElement('input');
                    inputBarang.type = 'hidden';
                    inputBarang.name = `barang_id[${index}]`;
                    inputBarang.value = item.barangId;
                    form.appendChild(inputBarang);

                    const inputJumlah = document.createElement('input');
                    inputJumlah.type = 'hidden';
                    inputJumlah.name = `jumlah[${index}]`;
                    inputJumlah.value = item.jumlah;
                    form.appendChild(inputJumlah);

                    const inputHarga = document.createElement('input');
                    inputHarga.type = 'hidden';
                    inputHarga.name = `harga_satuan[${index}]`;
                    inputHarga.value = item.harga;
                    form.appendChild(inputHarga);
                });

            });


            // Inisialisasi
            updateStokInfo();
            updateTotal();
        });
    </script>
@endsection

@section('styles')
    <style>
        .table th {
            vertical-align: middle;
        }

        #stok-info {
            display: block;
            min-height: 20px;
            /* tinggi tetap */
            margin-top: 5px;
            font-weight: 500;
        }


        .is-invalid {
            border-color: #dc3545;
        }

        #tabel-transaksi td,
        #tabel-transaksi th {
            vertical-align: middle;
        }

        .btn-hapus {
            min-width: 40px;
        }
    </style>
@endsection
