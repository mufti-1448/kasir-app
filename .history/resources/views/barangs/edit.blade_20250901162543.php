@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Barang: {{ $barang->nama }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('barangs.update', $barang) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('barangs._form')
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('barangs.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
