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


    // split string into array on uppercase letter and turn it into string
    public static function name_EN($name)
    {
        return trim(implode(' ', preg_split('/(?=[A-Z])/', $name))) ?? $name;
    }

    public function recipes()
    {
        return $this->belongsToManyThrough(Recipe::class, Resource::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
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
        return ItemType::toString($this->sharedData->item_type);
    }
}
