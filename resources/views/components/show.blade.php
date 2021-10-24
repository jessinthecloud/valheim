
<x-slot name="title">
    {{ $title }}
</x-slot>

@if(!empty($entity))
    @include($partial, [
        $index => $entity
    ])
@else
    No {{ $entityClassType }} to display.
@endif
