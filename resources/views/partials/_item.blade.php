<section class="max-w-full flex-grow bg-gray-800 mb-4 mr-4 p-6 md:w-5/12">
    <h2 class="w-full text-3xl mb-4">{{ $item->name }}</h2>
    <p class="block my-4">
        {{ $item->sharedData->description }} 
        <em class="block mt-1">{{ $item->itemType() }}</em>
    </p>
    @if($item->recipe)
            <p class="mt-4">
                <a class="inline-block my-1 py-3 px-6 bg-amber-900 font-semibold hover:bg-amber-200 hover:text-black transition ease-in-out duartion-150" 
                    href="{{ route('recipes.show', $item->recipe->slug) }}"
                >
                    Recipe
                </a>
            </p> 
        @endif
</section>
