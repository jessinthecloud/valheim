<?php

namespace App\Models\Items\Traits;

use App\Enums\ItemType;
use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Items\SharedData;

trait HasSharedData
{
    // Indices of the converted json array that correspond to
    // relationships and should not be directly inserted
    // index_name => relationFunctionName ; to allow array_intersect_key comparison
    public static array $relation_indices = [
        'shared_data' => [
            'method' => 'sharedData',
            'class' => SharedData::class,
            'relation' => 'associate',
        ],
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'sharedData',
    ];

    // -- SCOPES -----------------------------------------------------

    public function scopeWeapon($query)
    {
        return $query->whereHas( 'sharedData', function($query){
            $query->whereIn('item_type', CraftableItem::WEAPON_TYPES);
        } );
    }

    public function scopeArmor($query)
    {
        return $query->whereHas( 'sharedData', function($query){
            $query->whereIn('item_type', CraftableItem::ARMOR_TYPES);
        } );
    }

    public function scopeConsumable($query)
    {
        return $query->whereHas( 'sharedData', function($query){
            $query->where('item_type', ItemType::Consumable);
        } );
    }

// -- RELATIONSHIPS -----------------------------------------------

    public function sharedData()
    {
        return $this->belongsTo( SharedData::class );
    }
    
// -- MISC ---------------------------------------

    /**
     * @required by Craftable
     *
     * shared_data item_type as string
     *
     * @return string   item type
     */
    public function type() : string
    {
        return $this->niceName( ItemType::toString( $this->sharedData->item_type ) );
    }

    public function weight()
    {
        return $this->sharedData->weight;
    }

    public function teleportable() : bool
    {
        return ( (int)$this->sharedData->teleportable === 1 );
    }
}