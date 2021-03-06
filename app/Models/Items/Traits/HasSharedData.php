<?php

namespace App\Models\Items\Traits;

use App\Enums\ItemType;
use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Items\Item;
use App\Models\Items\SharedData;
use App\Models\Items\StatusEffect;

trait HasSharedData
{
    // constant can't be part of trait
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

// -- SCOPES -----------------------------------------------------

    public function scopeWeapon($query)
    {
        return $query->whereHas( 'sharedData', function($query){
            $query->whereIn('item_type', Item::WEAPON_TYPES);
        } );
    }

    public function scopeArmor($query)
    {
        return $query->whereHas( 'sharedData', function($query){
            $query->whereIn('item_type', Item::ARMOR_TYPES);
        } );
    }

    public function scopeConsumable($query)
    {
        return $query->whereHas( 'sharedData', function($query){
            $query->where('item_type', ItemType::Consumable);
        } );
    }

// -- RELATIONSHIPS -----------------------------------------------

    
// -- MISC ---------------------------------------

    /**
     * uniformity for description field
     * 
     * @return string|null
     */
    public function description() : ?string
    {
        return $this->sharedData->description;
    }

    public function weight()
    {
        return $this->sharedData->weight;
    }

    public function teleportable() : bool
    {
        return ( (int)$this->sharedData->teleportable === 1 );
    }
    
/*************************************
 * STATUS EFFECTS
 */
    public function attackEffect() : string
    {
        $effect = $this->sharedData->attackStatusEffect;
        
        return $effect->tooltip ?? (!empty($effect->name) ? $this->niceName($effect->name) : '');
    }

    public function consumeEffect() : string
    {
        return !empty($this->sharedData->consumeStatusEffect) ? $this->niceName($this->sharedData->consumeStatusEffect->name) : '';
    }

    public function equipEffect() : string
    {
        return !empty($this->sharedData->equipStatusEffect->name) ? $this->niceName($this->sharedData->equipStatusEffect->name) : '';
    }

    public function setEffect() : string
    {
        return !empty($this->sharedData->setStatusEffect->name) ? $this->niceName($this->sharedData->setStatusEffect->name) : '';
    }
}