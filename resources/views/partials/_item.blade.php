<section class="max-w-full flex-grow bg-gray-800 mb-4 mr-4 p-6 md:w-5/12">
    <h2 class="w-full relative text-3xl mb-4">{{ $item->name }} <span class="absolute top-0 right-0 text-gray-500 text-xs">{{ $item->id }}</span></h2>
    <p class="block my-4">
        {!! $item->sharedData->description !!} 
        <em class="block mt-1">{{ $item->itemType() }}</em>
    </p>
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
