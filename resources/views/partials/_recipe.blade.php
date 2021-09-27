<section class="max-w-full flex-grow flex flex-wrap justify-between bg-gray-800 mb-4 mr-4 p-6 md:w-5/12">
    <h2 class="relative w-full text-3xl mb-4">
        {{ $recipe->name }} <span class="absolute top-0 right-0 text-gray-500 text-xs">{{ $recipe->id }}</span>
    </h2>
    <div class="recipe-details w-full md:mr-6 md:w-1/2">
        @if($recipe->item)
            <p class="my-4">
                {!! $recipe->item->sharedData->description !!}
            </p>
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
    </div>
    
    @if($recipe->max_quality > 1 && !empty($recipe->upgrades))
        @include('partials._recipe_upgrades', [
            'recipe'    => $recipe
        ])
    @endif
    
</section>
