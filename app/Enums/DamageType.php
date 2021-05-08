<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class DamageType extends Enum
{
    const BLUNT = 0x1;
    const SLASH = 0x2;
    const PIERCE = 0x4;
    const CHOP = 0x8;
    const PICKAXE = 0x10;
    const FIRE = 0x20;
    const FROST = 0x40;
    const LIGHTNING = 0x80;
    const POISON = 0x100;
    const SPIRIT = 0x200;
    const PHYSICAL = 0x1F;
    const ELEMENTAL = 0xE;
}
