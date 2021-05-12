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
        <h1 class="w-full text-4xl mb-4">Recipes</h1>
        @foreach($recipes as $recipe)
            @include('partials._recipe', [
                'recipe'    => $recipe
            ])
        @endforeach
    @endif
    @if(!empty($items))
        <h1 class="w-full text-4xl mb-4">Items</h1>
        @foreach($items as $item)
            @include('partials._item', [
                'item'    => $item
            ])
        @endforeach
     @endif
   
@endsection
