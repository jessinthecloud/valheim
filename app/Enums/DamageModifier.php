<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class DamageModifier extends Enum
{
    const Normal = 0;
    const Resistant = 1;
    const Weak = 2;
    const Immune = 3;
    const Ignore = 4;
    const VeryResistant = 5;
    const VeryWeak = 6;
}
