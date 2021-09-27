<a {{ $attributes->merge([
    'class' => '
        relative flex flex-wrap items-center justify-between flex-grow bg-amber-900 mb-4 mr-4 p-6 transition ease-in-out duration-150 
        hover:bg-amber-200 
        hover:text-black
        focus:bg-amber-200 
        focus:text-black
    '
    ]) }}    
>
    {{ $slot }}
</a>