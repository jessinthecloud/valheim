<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class StatusAttribute extends Enum
{
    const NONE = 0;
    const COLDRESISTANCE = 1;
    const DOUBLEIMPACTDAMAGE = 2;
    const SAILINGPOWER = 4;
}
