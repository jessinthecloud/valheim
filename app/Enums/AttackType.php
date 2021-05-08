<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class AttackType extends Enum
{
    const HORIZONTAL = 0;
    const VERTICAL = 1;
    const PROJECTILE = 2;
    const NONE = 3;
    const AREA = 4;
    const TRIGGERPROJECTILE = 5;
}
