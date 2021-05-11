@extends('layouts.app')

@section('title')
    Valheim Recipes
@endsection

@section('content')
   @if(!empty($recipe))
        @include('partials._recipe', [
            'recipe'    => $recipe
        ])
    @endif
    @if(!empty($item))
        @include('partials._item', [
            'item'    => $item
        ])        
    @endif
    @if(!empty($recipes))
        <h1>Recipes</h1>
        @foreach($recipes as $recipe)
            @include('partials._recipe', [
                'recipe'    => $recipe
            ])
        @endforeach
    @endif
    @if(!empty($items))
        <h2>Items</h2>
        @foreach($items as $item)
            @include('partials._item', [
                'item'    => $item
            ])
        @endforeach
     @endif
   
@endsection
