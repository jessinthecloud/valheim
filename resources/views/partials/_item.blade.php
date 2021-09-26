<section class="max-w-full flex-grow bg-gray-800 mb-4 mr-4 p-6 md:w-5/12">
    <h2 class="w-full relative text-3xl mb-4">{{ $item->name }} <span class="absolute top-0 right-0 text-gray-500 text-xs">{{ $item->id }}</span></h2>
    <p class="block my-4">
        {!! $item->sharedData->description !!} 
        <em class="block mt-1">{{ $item->itemType() }}</em>
    </p>
    {{-- show link to details if not details page --}}
    @if(!in_array(Route::currentRouteName(), ['items.showSlug', 'items.show']))
        <p class="block my-4">
            <a class="inline-block my-1 py-3 px-6 bg-amber-900 font-semibold hover:bg-amber-200 hover:text-black transition ease-in-out duartion-150" 
                href="{{ route('items.show', $item->slug) }}"
            >
                View Details
            </a>
        </p>
    @else
        
        <h3 class="text-xl font-semibold">Details</h3>
        {{-- if weapon --}}
        @if($item->isWeapon())
            <table>
                <tr>
                    <td class="font-bold px-2 py-1">Attack Force:</td>
                    <td class="px-2 py-1">{{ $item->attack() }}</td>
                </tr>
                <tr>
                    <td class="font-bold px-2 py-1">Block Power:</td>
                    <td class="px-2 py-1">{{ $item->block() }}</td>
                </tr>
                @if(null !== $item->attackEffect())
                    <tr>
                    <td class="font-bold px-2 py-1">Effect:</td>
                    <td class="px-2 py-1">{{ $item->attackEffect() }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="font-bold px-2 py-1">Backstab Bonus:</td>
                    <td class="px-2 py-1">{{ $item->duration() }}</td>
                </tr>
            </table>
        @endif
        {{-- if armor --}}
        @if($item->isArmor())
            <table>
                <tr>
                    <td class="font-bold px-2 py-1">Armor:</td>
                    <td class="px-2 py-1">{{ $item->armor() }}</td>
                </tr>
                <tr>
                    <td class="font-bold px-2 py-1">Block Power:</td>
                    <td class="px-2 py-1">{{ $item->block() }}</td>
                </tr>
                @if(null !== $item->armorPerLevel())
                    <tr>
                        <td class="font-bold px-2 py-1">Armor Per Level:</td>
                        <td class="px-2 py-1">{{ $item->armorPerLevel() }}</td>
                    </tr>
                @endif
                @if(null !== $item->deflection() && $item->deflection() > 0)
                    <tr>
                        <td class="font-bold px-2 py-1">Deflection Force:</td>
                        <td class="px-2 py-1">{{ $item->deflection() }}</td>
                    </tr>
                @endif
                @if(null !== $item->deflectionPerLevel() && $item->deflectionPerLevel() > 0)
                    <tr>
                        <td class="font-bold px-2 py-1">Deflection Force Per Level:</td>
                        <td class="px-2 py-1">{{ $item->deflectionPerLevel() }}</td>
                    </tr>
                @endif
                @if(null !== $item->movementModifier() && abs($item->movementModifier()) !== 0)
                    <tr>
                        <td class="font-bold px-2 py-1">Movement Effect:</td>
                        <td class="px-2 py-1">{{ $item->movementEffect() }}</td>
                    </tr>
                @endif
            </table>
        @endif
        {{-- if food --}}
        @if($item->isFood())
            <table>
                <tr>
                    <td class="font-bold px-2 py-1">Health:</td>
                    <td class="px-2 py-1">{{ $item->health() }}</td>
                </tr>
                <tr>
                    <td class="font-bold px-2 py-1">Stamina:</td>
                    <td class="px-2 py-1">{{ $item->stamina() }}</td>
                </tr>
                <tr>
                    <td class="font-bold px-2 py-1">Health Regen:</td>
                    <td class="px-2 py-1">{{ $item->healthRegen() }}</td>
                </tr>
                <tr>
                    <td class="font-bold px-2 py-1">Duration:</td>
                    <td class="px-2 py-1">{{ $item->duration() }}</td>
                </tr>
            </table>
        @endif
    
        {{-- <div class="w-full"> {!! $item->image(new \App\Http\ImageFetcher) !!}</div> --}}

        {{--<div class="flex my-4 w-full">
            <div class="w-1/2">Info: </div>
            <div class="w-1/2"><?php dump($item->sharedData->getAttributes()); ?></div>
        </div>--}}
    @endif
    @if($item->recipes)
        @foreach($item->recipes as $key => $recipe)
            <section class="mt-4">
                <h3 class="w-full text-xl font-semibold mt-0 mb-0">Recipe @if($key > 0) {{ $key+1 }} @endif</h3>
                {{--<a class="inline-block my-1 py-3 px-6 bg-amber-900 font-semibold hover:bg-amber-200 hover:text-black transition ease-in-out duartion-150" 
                    href="{{ route('recipes.show', $recipe->slug) }}"
                >
                    Recipe @if($key > 0) {{ $key+1 }} @endif
                </a>--}}
                @include('partials._recipe')
            </section> 
        @endforeach
    @endif
</section>
