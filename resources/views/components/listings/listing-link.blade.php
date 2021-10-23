@props(['routeName', 'entity', 'entityName'])

<a
    href="{{ route($routeName, [$entityName => $entity->slug]) }} {{--{{ route($routeName, $entity->slug) }}--}}"
    class="relative w-full flex flex-wrap items-center justify-between flex-grow bg-gray-800 mb-4 p-6 transition ease-in-out duration-150 
        hover:bg-amber-200 
        hover:text-black
        focus:bg-amber-200 
        focus:text-black 
        md:mr-4
        md:w-1/3
        lg:w-1/5
    "
>
    <h2 class="relative w-full text-xl flex flex-wrap items-center">
        @if( !empty($entity->image))
            <img
                src="{{ $entity->image() }}"
                alt="{{ $entity->name }} Thumbnail"
                class="mr-4"
            />
        @endif
        
        {{ $entity->name }} 
        
        @if(config('app.env') === 'local')
            <span class="absolute top-0 right-0 text-gray-500 text-xs">{{ $entity->id }}</span>
        @endif
    </h2>
</a>
