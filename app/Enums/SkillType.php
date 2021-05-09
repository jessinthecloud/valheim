<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class SkillType extends Enum
{
    const None        = 0;
    const Swords      = 1;
    const Knives      = 2;
    const Clubs       = 3;
    const Polearms    = 4;
    const Spears      = 5;
    const Blocking    = 6;
    const Axes        = 7;
    const Bows        = 8;
    const FireMagic   = 9;
    const FrostMagic  = 10;
    const Unarmed     = 11;
    const Pickaxes    = 12;
    const Woodcutting = 13;
    const Jump        = 100;
    const Sneak       = 101;
    const Run         = 102;
    const Swim        = 103;
    const All         = 999;
}
