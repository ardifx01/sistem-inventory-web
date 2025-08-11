@extends('layouts.app')

@section('content')
    <h1>Daftar Barang</h1>
    <ul>
        @foreach($barangs as $barang)
            <li>{{ $barang->nama }} - {{ $barang->stok }}</li>
        @endforeach
    </ul>
@endsection
