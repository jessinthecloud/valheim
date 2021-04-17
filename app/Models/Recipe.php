<?php

namespace App\Models;

use App\Models\Adapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Adapter
{
    use HasFactory;

    public string $name;
    public int $amount; // the number of items created from the recipe
    public int $minStationLevel; // minimum station level needed to create item
    public array $resources; // array of Resource objects (which contain Item objects)
    public $craftingStation; // CraftingStation object/class used to create item
    // repairStation // seems unused so far?


    public function __construct(string $name='', int $amount=0, int $minStationLevel=1, CraftingStation $craftingStation=null, array $resources=[])
    {
        $this->name = $name;
        $this->amount = $amount;
        $this->minStationLevel = $minStationLevel;
        $this->craftingStation = $craftingStation;
        $this->resources = $resources;
    }

    /**
     * calculate the required station level for this item based on its quality
     * and the minimum station level
     *
     * @param int $quality quality level of item
     *
     * @return CraftingStation
     */
    public function GetRequiredStationLevel(int $quality) : int
    {
        return (max(1, $minStationLevel) + ($quality - 1));
    }

    // I have no idea what this is doing tbh
    public function GetRequiredStation(int $quality=0) : CraftingStation
    {
        return $this->craftingStation;
        /*if ((bool)$this->craftingStation) {
            return $this->craftingStation;
        }
        if ($quality > 1) {
            return $this->repairStation;
        }
        return null;*/
    }
}
