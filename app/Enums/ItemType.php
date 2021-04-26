<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class ItemType extends Enum
{
    const NONE = 0;
    const MATERIAL = 1;
    const CONSUMABLE = 2;
    const ONEHANDEDWEAPON = 3;
    const BOW = 4;
    const SHIELD = 5;
    const HELMET = 6;
    const CHEST = 7;
    const AMMO = 9;
    const CUSTOMIZATION = 10;
    const LEGS = 11;
    const HANDS = 12;
    const TROPHIE = 13;
    const TWOHANDEDWEAPON = 14;
    const TORCH = 0xf;
    const MISC = 0x10;
    const SHOULDER = 17;
    const UTILITY = 18;
    const TOOL = 19;
    const ATTACH_ATGEIR = 20;
}
