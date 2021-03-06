<div class="w-full flex flex-wrap flex-grow justify-between bg-gray-800 mb-4 p-6">
    <h2 class="w-full relative text-3xl mb-4">
        {{ $item->name }} 
        @if(config('app.env') === 'local') 
            <span class="absolute top-0 right-0 text-gray-500 text-xs">{{ $item->id }}</span>
        @endif
    </h2>
    <div class="item-details flex-grow md:mr-6">
        
        {!! $item->image() !!}
        
        <p class="block my-4">
            {!! $item->description() !!}
            
            <em class="block mt-2">{{ $item->type() }}</em>
            
        </p>
        
        <x-items.weapon-details
            :item="$item"
        ></x-items.weapon-details>
        
        <x-items.armor-details
            :item="$item"
        ></x-items.armor-details>
        
        <x-items.consumable-details
            :item="$item"
        ></x-items.consumable-details>

        @if($item->recipes)
            @foreach($item->recipes as $key => $recipe)
                @if($recipe->maxQuality() > 1 && !empty($recipe->upgrades()))
                    @include('partials._recipe_upgrades', [
                        'recipe'    => $recipe
                    ])
                @endif
            @endforeach
        @endif
        
    </div> {{-- end item-details --}}
    
    @if($item->recipes)
        @foreach($item->recipes as $key => $recipe)
            <section class="w-full bg-gray-700 mt-6 ml-4 p-4 md:w-1/3 md:mt-0">
                <h3 class="w-full text-xl font-semibold mt-0 mb-0">
                    Recipe @if($key > 0) {{ $key+1 }} @endif
                </h3>

                @include('partials._recipe-details')

                {{--<x-general.button-link
                    class="font-semibold mt-4"
                    href="--}}{{--{{ route('recipes.show', $recipe->slug) }}--}}{{--"
                >
                    View Recipe Details
                </x-general.button-link>--}}
            
            </section>
        @endforeach
    @endif
</div>
