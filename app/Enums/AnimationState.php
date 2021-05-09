<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class AnimationState extends Enum
{
    const Unarmed       = 'Unarmed';
    const OneHanded     = 'OneHanded';
    const TwoHandedClub = 'TwoHandedClub';
    const Bow           = 'Bow';
    const Shield        = 'Shield';
    const Torch         = 'Torch';
    const LeftTorch     = 'LeftTorch';
    const Atgeir        = 'Atgeir';
    const TwoHandedAxe  = 'TwoHandedAxe';
    const FishingRod    = 'FishingRod';
}
