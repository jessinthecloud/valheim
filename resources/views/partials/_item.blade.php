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
        <tr class="">
            <h3 class="text-lg font-semibold">Details</h3>
            {{-- if weapon --}}
            @if($item->isWeapon())
            <dl class="">
                <dt></dt>
                <dd></dd>
            </dl>
            @endif
            {{-- if food --}}
            @if($item->isFood())
            <table>
                <tr>
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
        </section>
        {{-- <div class="w-full"> {!! $item->image(new \App\Http\ImageFetcher) !!}</div> --}}

        {{--<div class="flex my-4 w-full">
            <div class="w-1/2">Info: </div>
            <div class="w-1/2"><?php dump($item->sharedData->getAttributes()); ?></div>
        </div>--}}
    @endif
    @if($item->recipes)
        @foreach($item->recipes as $key => $recipe)
            <p class="mt-4">
                <a class="inline-block my-1 py-3 px-6 bg-amber-900 font-semibold hover:bg-amber-200 hover:text-black transition ease-in-out duartion-150" 
                    href="{{ route('recipes.show', $recipe->slug) }}"
                >
                    Recipe @if($key > 0) {{ $key+1 }} @endif
                </a>
            </p> 
        @endforeach
    @endif
</section>
