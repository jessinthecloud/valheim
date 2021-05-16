@extends('layouts.app')

@section('title')
    Home |
@endsection

@section('content')

<h1 class="w-full text-4xl mb-4">Welcome!</h1>

<p>
    If your memory is as terrible as mine, 
    feel free to use the search or recipe menu to find the Valheim recipe that you need 
</p>

<section class="w-full flex flex-wrap mt-8">
    <h1 class="w-full text-4xl mb-4">Recipes</h1>
    @if(!empty($recipes))
        @foreach($recipes as $recipe)
            @include('partials._recipe', [
                'recipe'    => $recipe
            ])
        @endforeach
    @else 
        No recipes to display.
    @endif
</section>
    
@endsection
