@if(!in_array(Route::currentRouteName(), ['items.showSlug', 'items.show']))
    <section class="max-w-full flex-grow bg-gray-800 mb-4 mr-4 p-6 md:w-5/12">
@else
    <section class="max-w-full flex-grow bg-gray-800 mb-4 mr-4 px-6 md:w-5/12">
@endif

    @if(!in_array(Route::currentRouteName(), ['items.showSlug', 'items.show']))
        <h2 class="relative w-full text-3xl mb-4">
            {{ $recipe->name }} <span class="absolute top-0 right-0 text-gray-500 text-xs">{{ $recipe->id }}</span>
        </h2>
    @endif
    @if($recipe->item)
        @if(!in_array(Route::currentRouteName(), ['items.showSlug', 'items.show']))
            <p class="my-4">
                {!! $recipe->item->sharedData->description !!}
            </p>
        @endif
        @if($recipe->craftingStation)
            <p class="mt-4">
                <strong>Crafting station:</strong> 
                <em>
                    {{ $recipe->craftingStation->name }} 
                    @if($recipe->min_station_level > 1)
                        Level {{ $recipe->min_station_level }}
                    @endif
                </em>
            </p>
        @endif
        @if($recipe->amount > 1)
            <p class="">
                <strong>Creates:</strong> <em>{{ $recipe->amount }}</em>
            </p>
        @endif
    @endif
    <p class="my-2">
        <strong>Resources:</strong>
    </p>
    <ul class="pl-4">
        @foreach($recipe->requirements as $requirement)
            @if($requirement->amount > 0)
                <li>
                    <strong>{{ $requirement->amount }}</strong> 
                    <a 
                    class="underline hover:text-amber hover:no-underline transition ease-in-out duration-150" 
                    href="{{ route('items.show', $requirement->item->slug) }}">{{ $requirement->name }}</a>
                </li>
            @endif
        @endforeach
    </ul>
    @if(isset($is_listing) && $recipe->max_quality > 1)
        <p class="mt-4">
            <a 
                class="inline-block my-1 py-3 px-6 bg-amber-900 font-semibold hover:bg-amber-200 hover:text-black transition ease-in-out duration-150" 
                href="{{ route('recipes.show', $recipe->slug) }}"
            >
                View Details
            </a>
        </p>
    @endif 
    @if($recipe->max_quality > 1 && !empty($recipe->upgrades))
        @include('partials._recipe_upgrades', [
            'recipe'    => $recipe
        ])
    @endif
</section>
