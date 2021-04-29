@extends('layouts.app')

@section('title')
    Valheim Recipes
@endsection

@section('content')
    <h2>Recipes</h2>
    <ul>
        @foreach($recipes as $recipe)
            <li>{{ $recipe->name_EN() }}</li>
        @endforeach
    </ul>

    <h2>Items</h2>
    <ul>
        @foreach($items as $item)
            <li>{{ $item->name_EN() }}</li>
        @endforeach
    </ul>
@endsection
