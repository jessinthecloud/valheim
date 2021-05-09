@extends('layouts.app')

@section('title')
    Valheim Recipes
@endsection

@section('content')
    <h2>Recipes</h2>
    <ol>
        @foreach($recipes as $recipe)
            <li>{{ ucwords($recipe->name) }} ({{ $recipe->raw_name }}) -- {{ $recipe->slug }} ({{ $recipe->raw_slug }})</li>
            {{-- <?php dump($recipe); ?> --}}
        @endforeach
    </ol>

    <h2>Items</h2>
    <ol>
        @foreach($items as $item)
            <li>{{ $item->name }} ({{ $item->raw_name }}) -- {{ $item->itemType() }}</li>
            {{-- <?php dump($item); ?> --}}
            {{-- <?php dd($item->sharedData); ?> --}}
        @endforeach
    </ol>
@endsection
