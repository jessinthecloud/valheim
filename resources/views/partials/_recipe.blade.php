<section class="max-w-full flex-grow flex flex-wrap justify-between bg-gray-800 mb-4 mr-4 p-6 md:w-5/12">
    <h2 class="relative w-full text-3xl mb-4">
        {{ $recipe->name }} <span class="absolute top-0 right-0 text-gray-500 text-xs">{{ $recipe->id }}</span>
    </h2>
    <div class="recipe-details w-full md:mr-6 md:w-1/2">
        @if($recipe->item)
            <p class="my-4">
                {!! $recipe->item->sharedData->description !!}
            </p>
        @endif
        
        @include('partials._recipe-details')
    </div>

    @if($recipe->max_quality > 1 && !empty($recipe->upgrades))
        @include('partials._recipe_upgrades', [
            'recipe'    => $recipe
        ])
    @endif
    
    @if($recipe->relatedItems()->count() > 0)
        <div class="w-full bg-gray-900 mt-4 p-4">
            <h3 class="w-full text-xl font-semibold">Related Items</h3>
            <?php //if(null !== $recipe->item) { dump($recipe->item->id, $recipe->relatedItems()); } ?>
            <div class="flex flex-wrap flex-col h-40">
                @foreach($recipe->relatedItems() as $item)
                    <span class="pt-4 pr-4">
                        <x-general.link
                            class=""
                            href="{{ route('items.show', $item->slug) }}"
                        >
                            {{ $item->name }}
                        </x-general.link>
                    </span>
                @endforeach
            </div>
        </div>
    @endif
    
</section>
