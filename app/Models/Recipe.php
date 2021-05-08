<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [
        'resources',
        'crafting_station_id',
        'repair_station_id',
    ];

    // remove Recipe_ prefix
    public static function name_EN($name)
    {
        $name = trim(implode(' ', preg_split('/(?=[A-Z])/', $name))) ?? $name;
        return (explode('_', $name)[1]) ?? $name;
    }

    public function craftingStation()
    {
        return $this->belongsTo(CraftingStation::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
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
