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

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'item',
        'craftingStation',
        // 'requirements',
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

    public function repairStation()
    {
        return $this->belongsTo(CraftingStation::class, 'repair_station_id');
    }

    public function requirements()
    {
        return $this->belongsToMany(Requirement::class/*, 'recipe_requirement', 'recipe_id', 'requirement_id'*/);
    }

    // item this recipe creates
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * calculate the required station level for this item based on its quality
     * and the minimum station level
     *
     * @param int $quality quality level of item
     *
     * @return CraftingStation
     */
    public function getRequiredStationLevel(int $quality) : int
    {
        return (max(1, $this->min_station_level) + ($quality - 1));
    }

    public function getRequiredStation(int $quality=0) : ?CraftingStation
    {
        if ($quality > 1) {
            return $this->repairStation ?? $this->craftingStation ?? null;
        }

        return $this->craftingStation ?? null;
    }
}
