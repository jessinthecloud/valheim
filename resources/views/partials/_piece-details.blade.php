@if($requirement->item)
    @if($requirement->craftingStation)
        <p class="mt-4">
            <strong>Crafting station:</strong>
            <em>
                {{ $requirement->craftingStation->name }}
                @if($requirement->min_station_level > 1)
                    Level {{ $requirement->min_station_level }}
                @endif
            </em>
        </p>
    @endif
@endif
<ul class="pl-4">
    @if($requirement->amount > 0)
        <li>
            <strong>{{ $requirement->amount }}</strong>
            <a
                class="underline hover:text-amber hover:no-underline transition ease-in-out duration-150"
                href="{{ route('items.show', $requirement->item->slug) }}"
            >
                {{ $requirement->name }}
            </a>
        </li>
    @endif
</ul>