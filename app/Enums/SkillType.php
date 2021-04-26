<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class SkillType extends Enum
{
    const NONE        = 0;
    const SWORDS      = 1;
    const KNIVES      = 2;
    const CLUBS       = 3;
    const POLEARMS    = 4;
    const SPEARS      = 5;
    const BLOCKING    = 6;
    const AXES        = 7;
    const BOWS        = 8;
    const FIREMAGIC   = 9;
    const FROSTMAGIC  = 10;
    const UNARMED     = 11;
    const PICKAXES    = 12;
    const WOODCUTTING = 13;
    const JUMP        = 100;
    const SNEAK       = 101;
    const RUN         = 102;
    const SWIM        = 103;
    const ALL         = 999;
}
