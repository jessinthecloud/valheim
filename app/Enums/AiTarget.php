<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class AiTarget extends Enum
{
    const ENEMY = 0;
    const FRIENDHURT = 1;
    const FRIEND = 2;
}
