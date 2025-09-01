<div class="form-group">
    <label for="nama">Nama Barang *</label>
    <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror"
           value="{{ old('nama', $barang->nama ?? '') }}" required>
    @error('nama')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="kategori">Kategori</label>
    <input type="text" name="kategori" id="kategori" class="form-control @error('kategori') is-invalid @enderror"
           value="{{ old('kategori', $barang->kategori ?? '') }}" list="kategori-list">
    <datalist id="kategori-list">
        @foreach($kategoris as $kat)
            <option value="{{ $kat }}">
        @endforeach
    </datalist>
    @error('kategori')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="harga_beli">Harga Beli *</label>
            <input type="number" name="harga_beli" id="harga_beli" class="form-control @error('harga_beli') is-invalid @enderror"
                   value="{{ old('harga_beli', $barang->harga_beli ?? '') }}" min="0" step="0.01" required>
            @error('harga_beli')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="harga_jual">Harga Jual *</label>
            <input type="number" name="harga_jual" id="harga_jual" class="form-control @error('harga_jual') is-invalid @enderror"
                   value="{{ old('harga_jual', $barang->harga_jual ?? '') }}" min="0" step="0.01" required>
            @error('harga_jual')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="stok">Stok *</label>
            <input type="number" name="stok" id="stok" class="form-control @error('stok') is-invalid @enderror"
                   value="{{ old('stok', $barang->stok ?? '0') }}" min="0" required>
            @error('stok')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="satuan">Satuan</label>
            <input type="text" name="satuan" id="satuan" class="form-control @error('satuan') is-invalid @enderror"
                   value="{{ old('satuan', $barang->satuan ?? '') }}">
            @error('satuan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
