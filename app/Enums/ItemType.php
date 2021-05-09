<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class ItemType extends Enum
{
    const None = 0;
    const Material = 1;
    const Consumable = 2;
    const OneHandedWeapon = 3;
    const Bow = 4;
    const Shield = 5;
    const Helmet = 6;
    const Chest = 7;
    const Ammo = 9;
    const Customization = 10;
    const Legs = 11;
    const Hands = 12;
    const Trophie = 13;
    const TwoHandedWeapon = 14;
    const Torch = 0xf;
    const Misc = 0x10;
    const Shoulder = 17;
    const Utility = 18;
    const Tool = 19;
    const Attach_atgeir = 20;
}
