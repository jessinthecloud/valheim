@extends('layouts.app')

@section('title')
    Valheim Recipes
@endsection

@section('content')
    <h2>Recipes</h2>
    <ol>
        @foreach($recipes as $recipe)
            <li>
                {{ ucwords($recipe->name) }} ({{ $recipe->raw_name }}) {{-- {{ $recipe->slug }} ({{ $recipe->raw_slug }}) --}}
                <ul>
                    @foreach($recipe->resources as $resource)
                        <li>
                            <strong>{{ $resource->name }}</strong> ({{ $resource->amount }})
                        </li>
                    @endforeach
                </ul>
            </li>
            {{-- <?php dd($recipe->resources); ?> --}}
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
