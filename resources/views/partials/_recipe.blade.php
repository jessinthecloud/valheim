<section>
    <h2>{{ ucwords($recipe->name) }}</h2>
    @if($recipe->item)
        <p>
            {{ $recipe->item->sharedData->description }}
        </p>
        <p>
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
    <ul>
        @foreach($recipe->requirements as $requirement)
            @if($requirement->amount > 0)
                <li>
                    <strong>{{ $requirement->amount }}</strong> {{ $requirement->name }}
                </li>
            @endif
        @endforeach
    </ul>
</section>
