@extends('layouts.app')

@section('title')
    @if(!empty($piece)) {{ $piece->name }} | @endif
@endsection

@section('content')
    @if(!empty($piece))
        @include('partials._piece', [
            'piece' => $piece
        ])  
    @else 
        No piece to display.      
    @endif
@endsection
