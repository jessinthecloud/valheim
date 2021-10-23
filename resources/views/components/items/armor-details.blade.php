@props(['item'])

{{-- if armor --}}
@if($item->isArmor())
<table>
    <tr>
        <td class="font-bold px-2 py-1">Armor:</td>
        <td class="px-2 py-1">{{ $item->armor() }}</td>
    </tr>
    <tr>
        <td class="font-bold px-2 py-1">Block Power:</td>
        <td class="px-2 py-1">{{ $item->block() }}</td>
    </tr>
    @if(null !== $item->armorPerLevel())
        <tr>
            <td class="font-bold px-2 py-1">Armor Per Level:</td>
            <td class="px-2 py-1">{{ $item->armorPerLevel() }}</td>
        </tr>
    @endif
    @if(null !== $item->deflection() && $item->deflection() > 0)
        <tr>
            <td class="font-bold px-2 py-1">Deflection Force:</td>
            <td class="px-2 py-1">{{ $item->deflection() }}</td>
        </tr>
    @endif
    @if(null !== $item->deflectionPerLevel() && $item->deflectionPerLevel() > 0)
        <tr>
            <td class="font-bold px-2 py-1">Deflection Force Per Level:</td>
            <td class="px-2 py-1">{{ $item->deflectionPerLevel() }}</td>
        </tr>
    @endif
    @if(null !== $item->movementModifier() && abs($item->movementModifier()) !== 0)
        <tr>
            <td class="font-bold px-2 py-1">Movement Effect:</td>
            <td class="px-2 py-1">{{ $item->movementEffect() }}</td>
        </tr>
    @endif
    @if( !empty($item->setEffect()) )
        <tr>
            <td class="font-bold px-2 py-1">Status Effect:</td>
            <td class="px-2 py-1">{{ $item->setEffect() }}</td>
        </tr>
    @endif
    @if( !empty($item->equipEffect()) )
        <tr>
            <td class="font-bold px-2 py-1">Equip Status Effect:</td>
            <td class="px-2 py-1">{{ $item->equipEffect() }}</td>
        </tr>
    @endif
</table>
@endif