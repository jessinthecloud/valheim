
<x-slot name="title">
    {{ $title }}
</x-slot>

@if(!empty($item))
    @include($partial, [
        $index => $item
    ])
@else
    No {{ $itemClassType }} to display.
@endif
