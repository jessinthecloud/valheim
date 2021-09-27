@if($recipe->item)
    @if($recipe->craftingStation)
        <p class="mt-4">
            <strong>Crafting station:</strong>
            <em>
                {{ $recipe->craftingStation->name }}
                @if($recipe->min_station_level > 1)
                    Level {{ $recipe->min_station_level }}
                @endif
            </em>
        </p>
    @endif
    @if($recipe->amount > 1)
        <p class="">
            <strong>Creates:</strong> <em>{{ $recipe->amount }}</em>
        </p>
    @endif
@endif
<p class="my-2">
    <strong>Resources:</strong>
</p>
<ul class="pl-4">
    @foreach($recipe->requirements as $requirement)
        @if($requirement->amount > 0)
            <li>
                <strong>{{ $requirement->amount }}</strong>
                <a
                        class="underline hover:text-amber hover:no-underline transition ease-in-out duration-150"
                        href="{{ route('items.show', $requirement->item->slug) }}">{{ $requirement->name }}</a>
            </li>
        @endif
    @endforeach
</ul>