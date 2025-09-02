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
                            <div class="row mb-3">
                                <div class="col-md-5">
                                    <label for="barang_id">Pilih Barang</label>
                                    <select id="barang_id" class="form-control">
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach ($barangs as $barang)
                                            <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_jual }}"
                                                data-nama="{{ $barang->nama }}" data-stok="{{ $barang->stok }}"
                                                data-kode="{{ $barang->kode }}">
                                                {{ $barang->kode }} - {{ $barang->nama }} (Stok: {{ $barang->stok }}) -
                                                Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted" id="stok-info"></small>
                                </div>
                                <div class="col-md-3">
                                    <label for="jumlah">Jumlah</label>
                                    <input type="number" id="jumlah" class="form-control" min="1" value="1">
                                </div>
                                <div class="col-md-4 d-flex align-items-end p">
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

            // Tampilkan info stok saat barang dipilih
            barangSelect.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    const stok = parseInt(selectedOption.getAttribute('data-stok'));
                    stokInfo.textContent = `Stok tersedia: ${stok}`;
                    stokInfo.className = 'text-muted';
                    jumlahInput.setAttribute('max', stok);
                } else {
                    stokInfo.textContent = '';
                }
            });

            // Format Rupiah
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            // Update total dan kembalian
            function updateTotal() {
                let total = items.reduce((sum, item) => sum + item.harga * item.jumlah, 0);
                totalEl.textContent = 'Rp ' + formatRupiah(total);

                const bayar = parseFloat(bayarEl.value) || 0;
                const kembali = bayar - total;
                kembaliEl.textContent = formatRupiah(kembali > 0 ? kembali : 0);

                // Validasi bayar
                if (bayar < total) {
                    bayarEl.classList.add('is-invalid');
                    kembaliEl.parentElement.classList.add('table-danger');
                } else {
                    bayarEl.classList.remove('is-invalid');
                    kembaliEl.parentElement.classList.remove('table-danger');
                }
            }

            // Tambah item ke keranjang
            btnTambah.addEventListener('click', function() {
                if (!barangSelect.value) {
                    alert('Pilih barang terlebih dahulu');
                    return;
                }

                const selectedOption = barangSelect.options[barangSelect.selectedIndex];
                const barangId = barangSelect.value;
                const nama = selectedOption.getAttribute('data-nama');
                const kode = selectedOption.getAttribute('data-kode');
                const harga = parseFloat(selectedOption.getAttribute('data-harga'));
                const stok = parseInt(selectedOption.getAttribute('data-stok'));
                const jumlah = parseInt(jumlahInput.value);

                if (isNaN(jumlah) || jumlah < 1) {
                    alert('Jumlah harus minimal 1');
                    return;
                }

                if (jumlah > stok) {
                    alert(`Stok tidak cukup. Stok tersedia: ${stok}`);
                    return;
                }

                // Cek apakah barang sudah ada di keranjang
                const existingItem = items.find(item => item.barangId === barangId);

                if (existingItem) {
                    const newJumlah = existingItem.jumlah + jumlah;
                    if (newJumlah > stok) {
                        alert(`Stok tidak cukup untuk menambah jumlah. Stok tersedia: ${stok}`);
                        return;
                    }
                    existingItem.jumlah = newJumlah;

                    // Update tabel
                    const row = tabelBody.querySelector(`tr[data-barang-id="${barangId}"]`);
                    row.cells[2].textContent = newJumlah;
                    row.cells[3].textContent = 'Rp ' + formatRupiah(newJumlah * harga);
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

                    // Hapus item
                    const btnHapus = newRow.querySelector('.btn-hapus');
                    btnHapus.addEventListener('click', function() {
                        items = items.filter(item => item.barangId !== barangId);
                        newRow.remove();
                        updateTotal();
                    });
                }

                updateTotal();
                jumlahInput.value = 1;
            });

            // Hitung kembalian saat bayar berubah
            bayarEl.addEventListener('input', updateTotal);

            // Validasi form sebelum submit
            form.addEventListener('submit', function(e) {
                if (items.length === 0) {
                    e.preventDefault();
                    alert('Tambahkan minimal satu item ke keranjang');
                    return;
                }

                const total = items.reduce((sum, item) => sum + item.harga * item.jumlah, 0);
                const bayar = parseFloat(bayarEl.value) || 0;
                if (bayar < total) {
                    e.preventDefault();
                    alert('Jumlah pembayaran kurang dari total transaksi');
                    return;
                }

                // Tambahkan input hidden untuk setiap item
                items.forEach((item, index) => {
                    const inputBarangId = document.createElement('input');
                    inputBarangId.type = 'hidden';
                    inputBarangId.name = `items[${index}][barang_id]`;
                    inputBarangId.value = item.barangId;
                    form.appendChild(inputBarangId);

                    const inputJumlah = document.createElement('input');
                    inputJumlah.type = 'hidden';
                    inputJumlah.name = `items[${index}][jumlah]`;
                    inputJumlah.value = item.jumlah;
                    form.appendChild(inputJumlah);

                    const inputHarga = document.createElement('input');
                    inputHarga.type = 'hidden';
                    inputHarga.name = `items[${index}][harga_satuan]`;
                    inputHarga.value = item.harga;
                    form.appendChild(inputHarga);
                });
            });

            // Inisialisasi
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
            margin-top: 5px;
        }

        .is-invalid {
            border-color: #dc3545;
        }
    </style>
@endsection
