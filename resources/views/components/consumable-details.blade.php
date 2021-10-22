@props(['item'])
{{-- if food --}}
@if($item->isFood())
<table>
    <tr>
        <td class="font-bold px-2 py-1">Health:</td>
        <td class="px-2 py-1">{{ $item->health() }}</td>
    </tr>
    <tr>
        <td class="font-bold px-2 py-1">Stamina:</td>
        <td class="px-2 py-1">{{ $item->stamina() }}</td>
    </tr>
    <tr>
        <td class="font-bold px-2 py-1">Health Regen:</td>
        <td class="px-2 py-1">{{ $item->healthRegen() }}</td>
    </tr>
    <tr>
        <td class="font-bold px-2 py-1">Duration:</td>
        <td class="px-2 py-1">{{ $item->duration() }}</td>
    </tr>
    @if( !empty($item->consumeEffect()) )
        <tr>
            <td class="font-bold px-2 py-1">Status Effect:</td>
            <td class="px-2 py-1">{{ $item->consumeEffect() }}</td>
        </tr>
    @endif
</table>
@endif