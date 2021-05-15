@extends('layouts.app')

@section('title')
    @if(!empty($item)) {{ $item->name }} | @endif
@endsection

@section('content')
    @if(!empty($item))
        @include('partials._item', [
            'item' => $item
        ])  
    @else 
        No item to display.      
    @endif
@endsection
