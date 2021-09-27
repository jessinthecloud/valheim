<?php
namespace App\Enums;

abstract class PieceCategory  extends Enum
{
    const Misc = 0;
    const Crafting = 1;
    const Building = 2;
    const Furniture = 3;
    const Max = 4;
    const All = 100;
}
