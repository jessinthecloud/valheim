<?php
namespace App\Enums;

use App\Enums\Enum;

// Character.Faction
abstract class Faction extends Enum
{
    const Players = 0;
    const AnimalsVeg = 1;
    const ForestMonsters = 2;
    const Undead = 3;
    const Demon = 4;
    const MountainMonsters = 5;
    const SeaMonsters = 6;
    const PlainsMonsters = 7;
    const Boss = 8;
}
