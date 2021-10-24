<section class="upgrades-wrapper w-full">
    <div class="bg-gray-900 w-full mt-4 p-4">
        <h3 class="w-full text-xl mb-4">Upgrades</h3>
        <ul class="w-full flex flex-wrap">
            @foreach($recipe->upgrades() as $level => $upgrade)
                <li class="bg-gray-800 mb-4 mr-2 p-4 flex-grow">
                    <h4 class="text-lg font-bold mb-2">
                        Level {{ $level }}
                        @if($level === $recipe->maxQuality())
                            (MAX)
                        @endif
                    </h4>
                    <ul class="">
                        <li>
                            @if(isset($upgrade['station']) && $upgrade['station_level'] > 1) 
                                <em class="block mb-2">{{ $upgrade['station'] }} Level {{ $upgrade['station_level'] }}</em>  
                            @endif
                        </li>
                        @isset($upgrade['resources'])
                            @foreach($upgrade['resources'] as $item => $amount)
                                <li>
                                    @if($amount > 0)
                                        <strong>{{ $amount }}</strong> {{ $item }}
                                    @endif
                                </li>
                            @endforeach
                        @endisset
                    </ul>
                </li>
            @endforeach
        </ul>
        <p class="mt-4">
            <em>
                Total resources to max level:<br> 
                {!! $recipe->upgradeTotals($recipe->upgrades()) !!}
            </em>
        </p>
    </div>
</section>
