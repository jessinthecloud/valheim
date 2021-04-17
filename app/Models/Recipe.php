<?php

namespace App\Models;

use App\Http\Controllers\JsonAdapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    public string $name;
    public string $name_EN; // English name
    public string $itemName; // $this->name converted to InternalId for item name
    public int $amount; // the number of items created from the recipe
    public int $minStationLevel; // minimum station level needed to create item
    public $craftingStation; // CraftingStation object/class used to create item
    public $repairStation; // seems unused so far?
    public array $resources; // array of Resource objects (which contain Item objects)


    public function __construct($data)
    {
        dump("RECIPE CLASS ");
        dump($data);
        // extract($data);
        $this->name = $data['name'];
        $this->itemName = $data['itemName'] ?? JsonAdapter::internalName($this->name);
        $this->name_EN = $data['name_EN'] ?? JsonAdapter::camelToEnglish($this->itemName);
        $this->amount = $data['amount'];
        $this->minStationLevel = $data['minStationLevel'];
        $this->craftingStation = $data['craftingStation'] ?? null;
        $this->resources = $data['resources'];
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
