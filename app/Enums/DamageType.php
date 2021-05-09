<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class DamageType extends Enum
{
    const Blunt = 0x1;
    const Slash = 0x2;
    const Pierce = 0x4;
    const Chop = 0x8;
    const Pickaxe = 0x10;
    const Fire = 0x20;
    const Frost = 0x40;
    const Lightning = 0x80;
    const Poison = 0x100;
    const Spirit = 0x200;
    const Physical = 0x1F;
    const Elemental = 0xE;
}
