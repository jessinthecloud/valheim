<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class AttackType extends Enum
{
    const Horizontal = 0;
    const Vertical = 1;
    const Projectile = 2;
    const None = 3;
    const Area = 4;
    const TriggerProjectile = 5;
}
