@extends('layouts.app')

@section('content')
    <h1>Tatanan Rak</h1>
    <ul>
        @foreach($raks as $rak)
            <li>Rak {{ $rak->nama }} - Lokasi: {{ $rak->lokasi }}</li>
        @endforeach
    </ul>
@endsection
