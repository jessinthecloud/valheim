<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class AnimationState extends Enum
{
    // these don't actually have a value in Piece.cs
    const NONE = 'None';
    const FIRE = 'Fire';
    const BED = 'Bed';
    const BANNER = 'Banner';
    const CHAIR = 'Chair';
}
