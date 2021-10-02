<?php

namespace App\Models;

use App\Enums\ItemType;
use App\Http\ImageFetcher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Craftable
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = ['shared_data_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['sharedData'];
    
    // ItemType values that are weapons
    public const WEAPON_TYPES = [3,4,9,14,20];

    // ItemType values that are armor pieces or items
    public const ARMOR_TYPES = [5,6,7,11,12,17];


    // split string into array on uppercase letter and turn it into string
    public static function name_EN($name)
    {
        return trim(implode(' ', preg_split('/(?=[A-Z])/', $name))) ?? $name;
    }

    /**
     * items can have multiple recipes for their variants
     * e.g., Bronze and 5 Bronze bars
     *
     * @return Eloquent relationship to Recipes
     */
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function recipeRequirements()
    {
        return $this->belongsToManyThrough(ItemRecipe::class, Requirement::class);
    }

    public function requirementFor()
    {
        return $this->belongsToManyThrough(ItemRecipe::class, Requirement::class);
    }

    public function sharedData()
    {
        return $this->belongsTo(SharedData::class);
    }
    
////////////////////////////////////////////////////////////////

    /**
     * shared_data item_type as string
     *
     * @return string   item type
     */
    public function type() : string
    {
        return $this->name_EN(ItemType::toString($this->sharedData->item_type));
    }

    public function weight()
    {
        return $this->sharedData->weight;
    }

    public function teleportable() : bool
    {
        return ((int)$this->sharedData->teleportable === 1);
    }

    public function image(ImageFetcher $fetcher)
    {
        return $fetcher->fetchImageHtmlString(Str::snake($this->name));
    }
    
/*
 * WEAPON METHODS
 */
    public function isWeapon() : bool
    {
        return in_array($this->sharedData->item_type, Item::WEAPON_TYPES);
    }

    

    public function isArmor() : bool
    {
        return in_array($this->sharedData->item_type, Item::ARMOR_TYPES);
    }

    
    
/*
 * FOOD METHODS
 */
    public function isFood() : bool
    {
        return ((int)$this->sharedData->item_type === 2);
    }

    
}
