<a {{ $attributes->merge([
    'class' => '
        underline text-amber-200 transition ease-in-out duration-150 
        hover:no-underline
        focus:no-underline
        focus:bg-amber-200
        focus:text-black
    '
    ]) }}
>{{ $slot }}</a>