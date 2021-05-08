<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class DamageModifier extends Enum
{
    const NORMAL = 0;
    const RESISTANT = 1;
    const WEAK = 2;
    const IMMUNE = 3;
    const IGNORE = 4;
    const VERYRESISTANT = 5;
    const VERYWEA = 6;
}
