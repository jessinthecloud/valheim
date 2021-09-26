@extends('layouts.app')

@section('title')
    Recipes |
@endsection

@section('content')
    <h1 class="w-full text-4xl mb-4">Recipes</h1>
    
    @if(!empty($formatted_recipes))
        {{--paging links--}}
        {{ $recipes->links() }}
        
        @foreach($formatted_recipes as $recipe)
            @include('partials._recipe', [
                'recipe'    => $recipe
            ])
        @endforeach

        {{--paging links--}}
        {{ $recipes->links() }}
    @else 
        No recipes to display.
    @endif
@endsection
