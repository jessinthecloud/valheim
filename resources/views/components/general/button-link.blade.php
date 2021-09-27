<a {{ $attributes->merge([
    'class' => '
        inline-block bg-amber-900 p-4 transition ease-in-out duration-150 
        hover:bg-amber-200 
        hover:text-black
        focus:bg-amber-200 
        focus:text-black
    '
    ]) }}    
>
    {{ $slot }}
</a>