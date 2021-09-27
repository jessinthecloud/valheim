@extends('layouts.app')

@section('title')
    Home |
@endsection

@section('content')

<h1 class="w-full text-4xl mb-4">Valheim Recipes</h1>

<p class="my-2">
    We're under construction, but you can still use the search box or menu to find the Valheim recipe that you need.
</p>
<p class="my-2">
    If you find any undocumented problems or have a request, feel free to <x-general.link rel="noopener nofollow" href="https://github.com/jessinthecloud/valheim/issues/new/choose">open an issue</x-general.link> on github
</p>

<section class="w-full flex flex-wrap mt-8">
    <x-general.button-link class="lg:max-w-sm" href="{{ route('recipes.index') }}">
        Recipes listing
    </x-general.button-link>
    <x-general.button-link class="lg:max-w-sm" href="{{ route('items.index') }}">
        Items listing
    </x-general.button-link>
</section>
    
@endsection
