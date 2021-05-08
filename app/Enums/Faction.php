<?php
namespace App\Enums;

use App\Enums\Enum;

// Character.Faction
abstract class Faction extends Enum
{
    const PLAYERS = 0;
    const ANIMALSVEG = 1;
    const FORESTMONSTERS = 2;
    const UNDEAD = 3;
    const DEMON = 4;
    const MOUNTAINMONSTERS = 5;
    const SEAMONSTERS = 6;
    const PLAINSMONSTERS = 7;
    const BOS = 8;
}
