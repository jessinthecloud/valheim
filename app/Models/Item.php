<?php

namespace App\Models;

use App\Enums\ItemType;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
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

    public function requirementForRecipes()
    {
        return $this->belongsToManyThrough(Recipe::class, Requirement::class);
    }

    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }

    public function sharedData()
    {
        return $this->belongsTo(SharedData::class);
    }

    /**
     * shared_data item_type as string
     *
     * @return string   item type
     */
    public function itemType() : string
    {
        return $this->name_EN(ItemType::toString($this->sharedData->item_type));
    }
}
