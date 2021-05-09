<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class AiTarget extends Enum
{
    const Enemy = 0;
    const Friendhurt = 1;
    const Friend = 2;
}
