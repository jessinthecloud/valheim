<div class="max-w-full flex flex-wrap flex-grow justify-between bg-gray-800 mb-4 p-6 
    md:w-5/12
">
    <h2 class="w-full relative text-3xl mb-4">
        {{ $item->name }} 
        @if(config('app.env') === 'local') 
            <span class="absolute top-0 right-0 text-gray-500 text-xs">{{ $item->id }}</span>
        @endif
    </h2>
    <div class="item-details flex-grow md:mr-6">
        <p class="block my-4">
            {!! $item->sharedData->description !!} 
            <em class="block mt-2">{{ $item->type() }}</em>
        </p>
        
        <h3 class="text-xl font-semibold mt-6">Details</h3>
        {{-- if weapon --}}
        @if(is_a($item, App\Models\Weapon::class))
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
                    <td class="px-2 py-1">{{ $item->backstab() }}</td>
                </tr>
            </table>
        @endif
        {{-- if armor --}}
        @if(is_a($item, App\Models\Armor::class))
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
        @if(is_a($item, App\Models\Consumable::class))
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
    </div> {{-- end item-details --}}
    
    {{-- <div class="w-full"> {!! $item->image(new \App\Http\ImageFetcher) !!}</div> --}}
    
    @if($item->recipes)
        @foreach($item->recipes as $key => $recipe)
            <section class="w-full bg-amber-900 mt-6 p-4 md:w-1/3 md:mt-0">
                <h3 class="w-full text-xl font-semibold mt-0 mb-0">
                    Recipe @if($key > 0) {{ $key+1 }} @endif
                </h3>

                @include('partials._recipe-details')

                @if($recipe->max_quality > 1)
                    <x-general.button-link
                        class="font-semibold mt-4"
                        href="{{ route('recipes.show', $recipe->slug) }}"
                    >
                        View Full Recipe
                    </x-general.button-link>
                @endif
            </section>
        @endforeach
    @endif

    {{--<div class="flex my-4 w-full">
        <div class="w-1/2">Info: </div>
        <div class="w-1/2"><?php dump($item->sharedData->getAttributes()); ?></div>
    </div>--}}
</div>
