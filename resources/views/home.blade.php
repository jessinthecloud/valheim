@extends('layouts.app')

@section('title')
    Home |
@endsection

@section('content')

<h1 class="w-full text-4xl mb-4">Welcome to Valheim Recipes</h1>

<p class="my-2">
    We're under construction, but you can still use the search box or menu to find the Valheim recipe that you need.
</p>
<p class="my-2">
    This site uses the item and recipe data extracted directly from the game, thanks to a modified version of JotunnDoc, from <a class="underline hover:no-underline"  rel="noopener nofollow" href="https://github.com/Valheim-Modding/Jotunn">Jötunn, the Valheim Library</a>, that I created using their excellent mod tools and tutorials. 
</p>
<p class="my-2">
    If you find any undocumented problems or have a request, feel free to <a class="underline hover:no-underline" rel="noopener nofollow" href="https://github.com/jessinthecloud/valheim/issues/new/choose">open an issue</a> on github
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
