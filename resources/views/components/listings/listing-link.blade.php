@props(['routeName', 'entity', 'entityName'])

<a
    href="{{ route($routeName, [$entityName => $entity->slug]) }} {{--{{ route($routeName, $entity->slug) }}--}}"
    class="relative w-full flex flex-wrap items-center justify-center flex-grow bg-gray-800 mb-4 p-6 transition ease-in-out duration-150 border rounded border-gray-700
        hover:bg-amber-200 
        hover:border-amber-400 
        hover:text-black
        focus:bg-amber-600 
        focus:text-black 
        md:mr-4
        md:w-1/3
        lg:w-1/5
    "
>
    @if( !empty($entity->image))
        <img
                src="{{ $entity->image() }}"
                alt="{{ $entity->name }} Thumbnail"
                class="w-1/4"
        />
    @endif
    
    <h2 class="relative w-3/4 flex flex-wrap items-center text-lg pl-4 m-0">
                
        {{ $entity->name }} 
        
        {{--@if(config('app.env') === 'local')
            <span class="absolute top-0 right-0 text-gray-500 text-xs">{{ $entity->id }}</span>
        @endif--}}
    </h2>
</a>
