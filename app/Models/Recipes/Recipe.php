<?php

namespace App\Models\Recipes;

use App\Models\Tools\CraftingStation;
use App\Models\Tools\RepairStation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class Recipe extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [
        /*'resources',
        'crafting_station_id',
        'repair_station_id',*/
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
    protected $with = [];

    // item this recipe creates
    abstract public function creation();
    
    // used to make the recipe; crafting station, piece table tool, etc
    abstract public function craftingDevice();

    // requirements for the recipe
    abstract public function requirements();


    public function craftingStation()
    {
        return $this->belongsTo(CraftingStation::class);
    }

    public function repairStation()
    {
        return $this->belongsTo(RepairStation::class);
    }
    
    public function relatedCraftables()
    {
        return Requirement::whereHas('item', function($query){
            $query->where('item_id', $this->item->id);
        })
        ->orWhereHas('pieces', function($query){
            $query->whereHas('requirements',  function($query){
                $query->where('item_id', $this->item->id);
            });
        })
        ->get()->unique('item_id');
    }

    /**
     * calculate the required station level for this item based on its quality
     * and the minimum station level
     *
     * @param int $quality quality level of item
     *
     * @return CraftingStation
     */
    public function requiredStationLevel(int $quality) : int
    {
        return (max(1, $this->min_station_level) + ($quality - 1));
    }

    public function requiredStation(int $quality=0) : ?CraftingStation
    {
        if ($quality > 1) {
            return $this->repairStation ?? $this->craftingStation ?? null;
        }

        return $this->craftingStation ?? null;
    }
}
