<?php
namespace App\Enums;

use App\Enums\Enum;

abstract class Biome extends Enum
{
    const None = 0;
    const Meadows = 1;
    const Swamp = 2;
    const Mountain = 4;
    const BlackForest = 8;
    const Plains = 0x10;
    const AshLands = 0x20;
    const DeepNorth = 0x40;
    const Ocean = 0x100;
    const Mistlands = 0x200;
    const BiomesMax = 513;
}
