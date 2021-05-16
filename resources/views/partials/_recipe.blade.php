<section class="max-w-full flex-grow bg-gray-800 mb-4 mr-4 p-6 md:w-5/12">
    <h2 class="w-full text-3xl mb-4">{{ $recipe->name }} Recipe</h2>
    @if($recipe->item)
        <p class="my-4">
            {{ $recipe->item->sharedData->description }}
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
                    <strong>{{ $requirement->amount }}</strong> {{ $requirement->name }}
                </li>
            @endif
        @endforeach
    </ul>
    @if($recipe->max_quality > 1)
        <h3 class="w-full text-xl mt-4">Upgrades:</h3>
        <div class="bg-gray-900 mt-4 p-4">
            @if(!empty($upgrades))
            <ul class="">
                @foreach($upgrades as $level => $upgrade)
                    <li class="bg-gray-800 mb-4 p-4">
                        <h4 class="text-lg font-bold mb-2">
                            Level {{ $level }}
                            @if($level === $recipe->max_quality)
                                (MAX)
                            @endif
                        </h4>
                        <ul class="pl-4">
                            <li>
                                @if(isset($upgrade['station']) && $upgrade['station_level'] > 1) 
                                    <em class="block mb-2">{{ $upgrade['station'] }} Level {{ $upgrade['station_level'] }}</em>  
                                @endif
                            </li>
                        @foreach($upgrade['resources'] as $item => $amount)
                            <li>
                                @if($amount > 0)
                                    <strong>{{ $amount }}</strong> {{ $item }}
                                @endif
                            </li>
                        @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
            <p class="mt-4">
                <em>Total Resources Needed: {!! $totals !!}</em>
            </p>
            @endif
        </div>
    @endif
</section>
