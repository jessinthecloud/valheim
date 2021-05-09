<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class StatusAttribute extends Enum
{
    const None = 0;
    const ColdResistance = 1;
    const DoubleImpactDamage = 2;
    const SailingPower = 4;
}
