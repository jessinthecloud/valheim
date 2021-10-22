@props(['item'])

@if($item->isWeapon())
<table>
    <tr>
        <td class="font-bold px-2 py-1">Attack Force:</td>
        <td class="px-2 py-1">{{ $item->attack() }}</td>
    </tr>
    <tr>
        <td class="font-bold px-2 py-1">Block Power:</td>
        <td class="px-2 py-1">{{ $item->block() }}</td>
    </tr>
    @if(null !== $item->attackEffect())
        <tr>
            <td class="font-bold px-2 py-1">Effect:</td>
            <td class="px-2 py-1">{{ $item->attackEffect() }}</td>
        </tr>
    @endif
    <tr>
        <td class="font-bold px-2 py-1">Backstab Bonus:</td>
        <td class="px-2 py-1">{{ $item->backstab() }}</td>
    </tr>
</table>
@endif