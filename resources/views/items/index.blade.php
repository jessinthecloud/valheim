@extends('layouts.app')

@section('title')
    Items |
@endsection

@section('content')
    <h1 class="w-full text-4xl mb-4">Items</h1>
    @if(!empty($items))
        @foreach($items as $item)
            @include('partials._item', [
                'item'    => $item
            ])
        @endforeach
    @else 
        No items to display.
    @endif
@endsection
