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
        <div class="bg-gray-900 mt-4 p-4">
            <p class="">
                <strong>Max Quality Level:</strong> {{ $recipe->max_quality }}
            </p>
            @if(!empty($upgrades))
            <ul class="mt-2 pl-4">
                @foreach($upgrades as $level => $upgrade)
                    <li>
                        <em>Level {{ $level }}</em>:
                        <ul class="pl-4">
                        @foreach($upgrade as $item => $amount)
                            @if($amount > 0)
                                <li><strong>{{ $amount }}</strong> {{ $item }}</li>
                            @endif
                        @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
            <p class="my-2">
                <em>Total Resources Needed: {!! $totals !!}</em>
            </p>
            @endif
        </div>
    @endif
</section>
