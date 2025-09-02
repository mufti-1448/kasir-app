@extends('layouts.app')

@section('title', 'Tambah Transaksi Baru')

@section('content')
     <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tambah Transaksi Baru</h5>
                        <a href="#" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form id="form-transaksi" method="POST" action="#">
                            <!-- Informasi Transaksi & Nama Pembeli -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nama_pembeli">Nama Pembeli</label>
                                        <input type="text" name="nama_pembeli" id="nama_pembeli" class="form-control"
                                            placeholder="Masukkan nama pembeli (opsional)">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tanggal">Tanggal</label>
                                        <input type="text" class="form-control" value="27/05/2024 14:30" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kasir">Kasir</label>
                                        <input type="text" class="form-control" value="Admin" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Pilih Barang -->
                            <div class="row mb-3">
                                <div class="col-md-5">
                                    <label for="barang_id">Pilih Barang</label>
                                    <select id="barang_id" class="form-control">
                                        <option value="">-- Pilih Barang --</option>
                                        <option value="1" data-harga="15000" data-nama="Buku Tulis" data-stok="50" data-kode="B001">
                                            B001 - Buku Tulis (Stok: 50) - Rp 15.000
                                        </option>
                                        <option value="2" data-harga="5000" data-nama="Pensil" data-stok="100" data-kode="P001">
                                            P001 - Pensil (Stok: 100) - Rp 5.000
                                        </option>
                                        <option value="3" data-harga="8000" data-nama="Penghapus" data-stok="80" data-kode="P002">
                                            P002 - Penghapus (Stok: 80) - Rp 8.000
                                        </option>
                                        <option value="4" data-harga="12000" data-nama="Penggaris" data-stok="40" data-kode="P003">
                                            P003 - Penggaris (Stok: 40) - Rp 12.000
                                        </option>
                                    </select>
                                    <small class="text-muted" id="stok-info"></small>
                                </div>
                                <div class="col-md-3">
                                    <label for="jumlah">Jumlah</label>
                                    <input type="number" id="jumlah" class="form-control" min="1" value="1">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
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
                                                    min="0" required value="0">
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
            console.log('Document loaded, initializing script...'); // Debug

            const barangSelect = document.getElementById('barang_id');
            const jumlahInput = document.getElementById('jumlah');
            const btnTambah = document.getElementById('btn-tambah');
            const tabelBody = document.querySelector('#tabel-transaksi tbody');
            const totalEl = document.getElementById('total');
            const bayarEl = document.getElementById('bayar');
            const kembaliEl = document.getElementById('kembali');
            const stokInfo = document.getElementById('stok-info');
            const form = document.getElementById('form-transaksi');

            // Debug elements
            console.log('Elements:', {
                barangSelect,
                jumlahInput,
                btnTambah,
                tabelBody,
                totalEl,
                bayarEl,
                kembaliEl,
                stokInfo,
                form
            });

            let items = [];
            let total = 0;

            // Tampilkan info stok saat barang dipilih
            if (barangSelect) {
                barangSelect.addEventListener('change', function() {
                    console.log('Barang selected:', this.value); // Debug
                    if (this.value) {
                        const selectedOption = this.options[this.selectedIndex];
                        const stok = parseInt(selectedOption.getAttribute('data-stok'));
                        stokInfo.textContent = `Stok tersedia: ${stok}`;
                        stokInfo.className = 'text-muted';

                        // Set nilai maksimum untuk input jumlah
                        jumlahInput.setAttribute('max', stok);
                    } else {
                        stokInfo.textContent = '';
                    }
                });
            }

            // Format angka ke format Rupiah
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            // Fungsi untuk update total dan kembalian
            function updateTotal() {
                total = items.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
                if (totalEl) totalEl.textContent = 'Rp ' + formatRupiah(total);

                const bayar = parseFloat(bayarEl.value) || 0;
                const kembali = bayar - total;
                if (kembaliEl) kembaliEl.textContent = formatRupiah(kembali > 0 ? kembali : 0);

                // Beri warna berdasarkan status bayar
                if (bayarEl) {
                    if (bayar < total) {
                        bayarEl.classList.add('is-invalid');
                        if (kembaliEl) kembaliEl.parentElement.classList.add('table-danger');
                    } else {
                        bayarEl.classList.remove('is-invalid');
                        if (kembaliEl) kembaliEl.parentElement.classList.remove('table-danger');
                    }
                }
            }

            // Tambah item ke keranjang
            if (btnTambah) {
                btnTambah.addEventListener('click', function() {
                    console.log('Tambah button clicked'); // Debug

                    if (!barangSelect || !barangSelect.value) {
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

                    console.log('Adding item:', {
                        barangId,
                        nama,
                        harga,
                        stok,
                        jumlah
                    }); // Debug

                    if (isNaN(jumlah) || jumlah < 1) {
                        alert('Jumlah harus minimal 1');
                        return;
                    }

                    if (jumlah > stok) {
                        alert(`Stok tidak cukup. Stok tersedia: ${stok}`);
                        return;
                    }

                    // Cek apakah barang sudah ada di keranjang
                    const existingItemIndex = items.findIndex(item => item.barangId == barangId);

                    if (existingItemIndex >= 0) {
                        // Jika sudah ada, update jumlahnya
                        const newJumlah = items[existingItemIndex].jumlah + jumlah;

                        if (newJumlah > stok) {
                            alert(`Stok tidak cukup untuk menambah jumlah. Stok tersedia: ${stok}`);
                            return;
                        }

                        items[existingItemIndex].jumlah = newJumlah;

                        // Update tampilan
                        const row = tabelBody.rows[existingItemIndex];
                        row.cells[2].textContent = newJumlah;
                        row.cells[3].textContent = 'Rp ' + formatRupiah(newJumlah * harga);
                        row.dataset.subtotal = newJumlah * harga;
                    } else {
                        // Jika belum ada, tambahkan item baru
                        const newItem = {
                            barangId: barangId,
                            kode: kode,
                            nama: nama,
                            harga: harga,
                            jumlah: jumlah
                        };

                        items.push(newItem);

                        // Tambahkan baris baru di tabel
                        const newRow = tabelBody.insertRow();
                        newRow.dataset.subtotal = jumlah * harga;
                        newRow.innerHTML = `
                        <td>${kode} - ${nama}</td>
                        <td>Rp ${formatRupiah(harga)}</td>
                        <td>${jumlah}</td>
                        <td>Rp ${formatRupiah(jumlah * harga)}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger btn-hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;

                        // Tambahkan event listener untuk tombol hapus
                        newRow.querySelector('.btn-hapus').addEventListener('click', function() {
                            const index = items.findIndex(item => item.barangId == barangId);
                            if (index >= 0) {
                                items.splice(index, 1);
                                newRow.remove();
                                updateTotal();
                            }
                        });
                    }

                    updateTotal();
                    jumlahInput.value = 1;
                });
            }

            // Hitung kembalian saat input bayar berubah
            if (bayarEl) {
                bayarEl.addEventListener('input', updateTotal);
            }

            // Validasi form sebelum submit
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (items.length === 0) {
                        e.preventDefault();
                        alert('Tambahkan minimal satu item ke keranjang');
                        return;
                    }

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
                        inputBarangId.name = `barang_id[${index}]`;
                        inputBarangId.value = item.barangId;
                        form.appendChild(inputBarangId);

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

                    console.log('Form submitted with items:', items); // Debug
                });
            }

            // Inisialisasi tampilan
            updateTotal();
            console.log('Script initialization completed'); // Debug
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



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Document loaded, initializing script...');

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
            let total = 0;

            // Tampilkan info stok saat barang dipilih
            if (barangSelect) {
                barangSelect.addEventListener('change', function() {
                    if (this.value) {
                        const selectedOption = this.options[this.selectedIndex];
                        const stok = parseInt(selectedOption.getAttribute('data-stok'));
                        stokInfo.textContent = `Stok tersedia: ${stok}`;
                        stokInfo.className = 'text-muted';

                        // Set nilai maksimum untuk input jumlah
                        jumlahInput.setAttribute('max', stok);
                    } else {
                        stokInfo.textContent = '';
                    }
                });
            }

            // Format angka ke format Rupiah
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            // Fungsi untuk update total dan kembalian
            function updateTotal() {
                total = items.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
                if (totalEl) totalEl.textContent = 'Rp ' + formatRupiah(total);

                const bayar = parseFloat(bayarEl.value) || 0;
                const kembali = bayar - total;
                if (kembaliEl) kembaliEl.textContent = formatRupiah(kembali > 0 ? kembali : 0);

                // Beri warna berdasarkan status bayar
                if (bayarEl) {
                    if (bayar < total) {
                        bayarEl.classList.add('is-invalid');
                        if (kembaliEl) kembaliEl.parentElement.classList.add('table-danger');
                    } else {
                        bayarEl.classList.remove('is-invalid');
                        if (kembaliEl) kembaliEl.parentElement.classList.remove('table-danger');
                    }
                }
            }

            // Tambah item ke keranjang
            if (btnTambah) {
                btnTambah.addEventListener('click', function() {
                    console.log('Tambah button clicked');

                    if (!barangSelect || !barangSelect.value) {
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

                    console.log('Adding item:', {
                        barangId,
                        nama,
                        harga,
                        stok,
                        jumlah
                    });

                    if (isNaN(jumlah) || jumlah < 1) {
                        alert('Jumlah harus minimal 1');
                        return;
                    }

                    if (jumlah > stok) {
                        alert(`Stok tidak cukup. Stok tersedia: ${stok}`);
                        return;
                    }

                    // Cek apakah barang sudah ada di keranjang
                    const existingItemIndex = items.findIndex(item => item.barangId == barangId);

                    if (existingItemIndex >= 0) {
                        // Jika sudah ada, update jumlahnya
                        const newJumlah = items[existingItemIndex].jumlah + jumlah;

                        if (newJumlah > stok) {
                            alert(`Stok tidak cukup untuk menambah jumlah. Stok tersedia: ${stok}`);
                            return;
                        }

                        items[existingItemIndex].jumlah = newJumlah;

                        // Update tampilan
                        const row = tabelBody.rows[existingItemIndex];
                        row.cells[2].textContent = newJumlah;
                        row.cells[3].textContent = 'Rp ' + formatRupiah(newJumlah * harga);
                        row.dataset.subtotal = newJumlah * harga;
                    } else {
                        // Jika belum ada, tambahkan item baru
                        const newItem = {
                            barangId: barangId,
                            kode: kode,
                            nama: nama,
                            harga: harga,
                            jumlah: jumlah
                        };

                        items.push(newItem);

                        // Tambahkan baris baru di tabel
                        const newRow = tabelBody.insertRow();
                        newRow.dataset.subtotal = jumlah * harga;
                        newRow.innerHTML = `
                            <td>${kode} - ${nama}</td>
                            <td>Rp ${formatRupiah(harga)}</td>
                            <td>${jumlah}</td>
                            <td>Rp ${formatRupiah(jumlah * harga)}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger btn-hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;

                        // Tambahkan event listener untuk tombol hapus
                        newRow.querySelector('.btn-hapus').addEventListener('click', function() {
                            const index = items.findIndex(item => item.barangId == barangId);
                            if (index >= 0) {
                                items.splice(index, 1);
                                newRow.remove();
                                updateTotal();
                            }
                        });
                    }

                    updateTotal();
                    jumlahInput.value = 1;
                });
            }

            // Hitung kembalian saat input bayar berubah
            if (bayarEl) {
                bayarEl.addEventListener('input', updateTotal);
            }

            // Validasi form sebelum submit
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (items.length === 0) {
                        e.preventDefault();
                        alert('Tambahkan minimal satu item ke keranjang');
                        return;
                    }

                    const bayar = parseFloat(bayarEl.value) || 0;
                    if (bayar < total) {
                        e.preventDefault();
                        alert('Jumlah pembayaran kurang dari total transaksi');
                        return;
                    }

                    // Simulasi pengiriman data
                    e.preventDefault();
                    alert('Transaksi berhasil disimpan!');
                    console.log('Items yang dibeli:', items);
                    console.log('Total:', total);
                    console.log('Bayar:', bayar);
                    console.log('Kembali:', bayar - total);

                    // Reset form
                    items = [];
                    tabelBody.innerHTML = '';
                    updateTotal();
                    form.reset();
                });
            }

            // Inisialisasi tampilan
            updateTotal();
            console.log('Script initialization completed');
        });
    </script>
</body>
</html>
