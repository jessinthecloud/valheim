<div class="max-w-full flex flex-wrap flex-grow justify-between bg-gray-800 mb-4 mr-4 p-6 md:w-5/12">
    <h2 class="w-full relative text-3xl mb-0">
        {{ $piece->name }} 
        @if(config('app.env') === 'local') 
            <span class="absolute top-0 right-0 text-gray-500 text-xs">{{ $piece->id }}</span>
        @endif
    </h2>
    
    <div class="item-details flex-grow md:mr-6">
        <p class="block my-4">
            @if(null !== $piece->description) {!! $piece->description !!} @endif 
            
            <em class="block mt-1">{{ $piece->type() }}</em>
        </p>
        
        <h3 class="text-xl font-semibold mb-4">Details</h3>

        <p class="mb-4">
            <strong>Crafting Tool:</strong>
            <em>{{ $piece->pieceTable->name }}</em>
        </p>

        @if($piece->craftingStation)
            <p class="mb-4">
                <strong>Crafting station:</strong>
                <em>
                    {{ $requirement->craftingStation->name }}
                    @if($requirement->min_station_level > 1)
                        Level {{ $requirement->min_station_level }}
                    @endif
                </em>
            </p>
        @endif
        
        {{-- if weapon --}}
        @if($piece->isFurniture())
            {{--<table>
                <tr>
                    <td class="font-bold px-2 py-1">Attack Force:</td>
                    <td class="px-2 py-1">{{ $piece->attack() }}</td>
                </tr>
                <tr>
                    <td class="font-bold px-2 py-1">Block Power:</td>
                    <td class="px-2 py-1">{{ $piece->block() }}</td>
                </tr>
                @if(null !== $piece->attackEffect())
                    <tr>
                    <td class="font-bold px-2 py-1">Effect:</td>
                    <td class="px-2 py-1">{{ $piece->attackEffect() }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="font-bold px-2 py-1">Backstab Bonus:</td>
                    <td class="px-2 py-1">{{ $piece->backstab() }}</td>
                </tr>
            </table>--}}
        @endif
        
    </div> {{-- end item-details --}}
    {{-- <div class="w-full"> {!! $piece->image(new \App\Http\ImageFetcher) !!}</div> --}}
    
    @if($piece->requirements)
        <section class="w-full bg-gray-900 mt-4 p-4">
            <h3 class="w-full text-xl font-semibold mt-0 mb-2">
                Resources
            </h3>
            <ul class="pl-4">
                @foreach($piece->requirements->unique('slug') as $key => $requirement)
    
                    @if($requirement->amount > 0)
                        <li>
                            <strong>{{ $requirement->amount }}</strong>
                            <a
                                    class="underline hover:text-amber hover:no-underline transition ease-in-out duration-150"
                                    href="{{ route('items.show', $requirement->item->slug) }}"
                            >
                                {{ $requirement->name }}
                            </a>
                        </li>
                    @endif
    
                    {{--@if($requirement->max_quality > 1)
                        <x-general.button-link
                            class="font-semibold mt-4"
                            href="{{ route('requirements.show', $requirement->slug) }}"
                        >
                            View Full Recipe
                        </x-general.button-link>
                    @endif--}}
                @endforeach
            </ul>
        </section> 
    @endif

    {{--<div class="flex my-4 w-full">
    <div class="w-1/2">Info: </div>
    <div class="w-1/2"><?php dump($piece->sharedData->getAttributes()); ?></div>
</div>--}}
</div>
