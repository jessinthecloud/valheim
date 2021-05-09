<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class ComfortGroup extends Enum
{
    // these don't actually have a value in Piece.cs
    const None = 'None';
    const Fire = 'Fire';
    const Bed = 'Bed';
    const Banner = 'Banner';
    const Chair = 'Chair';
}
