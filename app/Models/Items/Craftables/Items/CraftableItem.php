<?php

namespace App\Models\Items\Craftables\Items;

use App\Enums\ItemType;
use App\Models\Items\Item;
use App\Models\Items\Contracts\IsCategorizable;
use App\Models\Items\Contracts\IsCraftable;
use App\Models\Items\Traits\HasRecipe;
use App\Models\Items\Traits\HasSharedData;
use App\Models\Recipes\ItemRecipe;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CraftableItem extends Item implements IsCraftable, IsCategorizable
{
    use HasFactory, HasSharedData, HasRecipe;
    
    protected $table = 'items';

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
//    protected $hidden = [];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
//    protected $with = [];

    // ItemType values that are weapons
    public const WEAPON_TYPES = [
        ItemType::OneHandedWeapon,
        ItemType::Bow,
        ItemType::Ammo,
        ItemType::TwoHandedWeapon,
        ItemType::Attach_atgeir,
    ];

    // ItemType values that are armor pieces or items
    public const ARMOR_TYPES = [
        ItemType::Shield,
        ItemType::Helmet,
        ItemType::Chest,
        ItemType::Legs,
        ItemType::Hands,
        ItemType::Shoulder,
    ];

// -- GLOBAL SCOPES -----------------------------------------------------

    /**
     * items can have multiple recipes for their variants
     * e.g., Bronze and 5 Bronze bars
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipes()
    {
        return $this->hasMany( ItemRecipe::class, 'creation_id' );
    }

    /*************************************
     * WEAPON METHODS
     */
    public function isWeapon() : bool
    {
        return in_array( $this->sharedData->item_type, Item::WEAPON_TYPES );
    }

    /*************************************
     * ARMOR METHODS
     */
    public function isArmor() : bool
    {
        return in_array( $this->sharedData->item_type, Item::ARMOR_TYPES );
    }

    /*************************************
     * FOOD METHODS
     */
    public function isFood() : bool
    {
        return ( (int)$this->sharedData->item_type === ItemType::Consumable );
    }

    public function type() : string
    {
        // TODO: Implement type() method.
    }
}
