@extends('layouts.app')

@section('title')
    Valheim Recipes
@endsection

@section('content')
    @if(!empty($recipes))
        <h2>Recipes</h2>
        <ol>
            @foreach($recipes as $recipe)
                <li>
                    {{ ucwords($recipe->name) }} ({{ $recipe->true_name }}) {{-- {{ $recipe->slug }} ({{ $recipe->raw_slug }}) --}}
                    <ul>
                        @foreach($recipe->requirements as $requirement)
                            <li>
                                {{ $requirement->amount }} <strong>{{ $requirement->name }}</strong>
                            </li>
                        @endforeach
                    </ul>
                </li>
                {{-- <?php dd($recipe->requirements); ?> --}}
            @endforeach
        </ol>
    @endif
    @if(!empty($items))
        <h2>Items</h2>
        <ol>
            @foreach($items as $item)
                <li>{{ $item->name }} ({{ $item->true_name }}) -- {{ $item->itemType() }}</li>
                {{-- <?php dump($item); ?> --}}
                {{-- <?php dd($item->sharedData); ?> --}}
            @endforeach
        </ol>
    @endif
    @if(!empty($recipe))
        <h2>{{ ucwords($recipe->name) }}</h2>
        ({{ $recipe->true_name }})
        <ul>
            @foreach($recipe->requirements as $requirement)
                <li>
                    {{ $requirement->amount }} <strong>{{ $requirement->name }}</strong>
                </li>
            @endforeach
        </ul>
    @endif
    @if(!empty($item))
        <h2>{{ $item->name }}</h2>
        <p>{{ $item->sharedData->description }}</p>
        <p>{{ $item->itemType() }}</p>
        
    @endif
@endsection
