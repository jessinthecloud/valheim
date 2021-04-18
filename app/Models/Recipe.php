<?php

namespace App\Models;

use App\Http\Controllers\JsonAdapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

    /**
    * The attributes that should be cast.
    *
    * @var array
    */
    protected $casts = [
        'resources' => 'array',
    ];

    /**
     * Store bar in protected variable instead of attributes
     * Because bar is not set in attributes, Laravel will not try to save it to database
     */
    protected $resources;

    /**
     * Mutator method to set bar's value
     */
    public function setResourcesAttribute($value)
    {
        $this->resources = $value;
    }

    /**
     * Accessor method to retrieve bar's value
     */
    public function getBarAttribute()
    {
        return $this->resources;
    }


    /* public int $id; // DB id
     public string $name;
     public string $name_EN; // English name
     public string $internalName; // $this->name converted to InternalId for item name
     public int $amount; // the number of items created from the recipe
     public int $minStationLevel; // minimum station level needed to create item
     public $craftingStation; // CraftingStation object/class used to create item
     public $repairStation; // seems unused so far?
     public array $resources; // array of Resource objects (which contain Item objects)
*/

    public function __construct($data=null)
    {
        /*dump("*~*~*~*~*~*~*~*~*~*~*");
        dump("RECIPE CLASS ");
        dump($data);
        dump("*~*~*~*~*~*~*~*~*~*~*");*/
        // extract($data);
        $this->name = $data['name'] ?? '';
        $this->internalName = $data['itemName'] ?? JsonAdapter::internalName($this->name);
        $this->name_EN = $data['name_EN'] ?? JsonAdapter::camelToEnglish($this->internalName);
        $this->amount = $data['amount'] ?? 1;
        $this->minStationLevel = $data['minStationLevel'] ?? 1;
        $craftstation = isset($data['craftingStation']) ? new CraftingStation($data['craftingStation']) : null;
        $this->craftingStation = $craftStation/*JsonAdapter::createObject('craftingStation', $data['craftingStation'])*/ ?? null;

        $resources = [];
        if (isset($data['resources'])) {
            foreach ($data['resources'] as $resource) {
                $resources []= JsonAdapter::createObject('resource', $resource);
            }
        }
        $this->resources = $resources;
    }

    /*public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }
    public function setName_ENAttribute($value)
    {
        $this->attributes['name_EN'] = $value;
    }
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value;
    }
    public function setMinStationLevelAttribute($value)
    {
        $this->attributes['minStationLevel'] = $value;
    }
    public function setCraftingStationAttribute($value)
    {
        $this->attributes['craftingStation'] = $value;
    }
    public function setResourcesAttribute($value)
    {
        $this->attributes['resources'] = $value;
    }*/

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
