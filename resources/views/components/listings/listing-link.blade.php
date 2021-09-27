@props(['routeName', 'entity'])

<a
    href="{{ route($routeName, $entity->slug) }}"
    class="relative w-full flex flex-wrap items-center justify-between flex-grow bg-gray-800 mb-4 mr-4 p-6 transition ease-in-out duration-150 
        hover:bg-amber-200 
        hover:text-black
        focus:bg-amber-200 
        focus:text-black 
        md:w-1/3
        lg:w-1/5
    "
>
    <h2 class="relative w-full text-2xl">
        {{ $entity->name }} <span class="absolute top-0 right-0 text-gray-500 text-xs">{{ $entity->id }}</span>
    </h2>
</a>
