<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class AnimationState extends Enum
{
    const UNARMED       = 'Unarmed';
    const ONEHANDED     = 'OneHanded';
    const TWOHANDEDCLUB = 'TwoHandedClub';
    const BOW           = 'Bow';
    const SHIELD        = 'Shield';
    const TORCH         = 'Torch';
    const LEFTTORCH     = 'LeftTorch';
    const ATGEIR        = 'Atgeir';
    const TWOHANDEDAXE  = 'TwoHandedAxe';
    const FISHINGROD    = 'FishingRod';
}
