<section class="max-w-full flex-grow bg-gray-800 mb-4 p-6 md:w-5/12">
    <h2 class="w-full text-3xl mb-4">{{ ucwords($recipe->name) }}</h2>
    @if($recipe->item)
        <p class="my-4">
            {{ $recipe->item->sharedData->description }}
        </p>
        <p class="my-4">
            @if($recipe->craftingStation)
                <strong>Crafting station:</strong> 
                <em>
                    {{ $recipe->craftingStation->name }} 
                    @if($recipe->min_station_level > 1)
                        Level {{ $recipe->min_station_level }}
                    @endif
                </em>
            @endif
        </p>
    @endif
    <ul class="pl-4">
        @foreach($recipe->requirements->sortByDesc('name', SORT_NATURAL|SORT_FLAG_CASE)->sortByDesc('amount', SORT_NUMERIC)->all() as $requirement)
            @if($requirement->amount > 0)
                <li>
                    <strong>{{ $requirement->amount }}</strong> {{ $requirement->name }}
                </li>
            @endif
        @endforeach
    </ul>
</section>
