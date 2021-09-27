<section class="upgrades-wrapper w-full md:w-1/2 lg:w-1/3">
    <div class="bg-gray-900 mt-4 p-4">
        <h3 class="w-full text-xl mb-4">Upgrades</h3>
        <ul class="w-full">
            @foreach($recipe->upgrades as $level => $upgrade)
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
                                <em class="block mb-2">Level {{ $upgrade['station_level'] }} {{ $upgrade['station'] }} </em>  
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
            <em>
                Total resources to max level:<br> 
                {!! $recipe->totals !!}
            </em>
        </p>
    </div>
</section>
