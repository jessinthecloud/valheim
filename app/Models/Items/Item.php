<?php

namespace App\Models\Items;

use App\Enums\ItemType;
use App\Enums\PieceCategory;
use App\Http\ImageFetcher;
use App\Models\Items\Contracts\CanBeIngredient;
use App\Models\Items\Contracts\IsItem;
use App\Models\Items\Craftables\Items\Armor;
use App\Models\Items\Craftables\Items\Consumable;
use App\Models\Items\Craftables\Items\Weapon;
use App\Models\Recipes\ItemRecipe;
use App\Models\Recipes\PieceRecipe;
use App\Models\Recipes\Requirement;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Items and Pieces. Item could be craftable or a natural drop
// should really be abstract... but Requirement
// needs a way to get relation to ALL items, not just craftable
class Item extends Model implements CanBeIngredient
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];
    
    protected $table = 'items';

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [];

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

// -- ICONS - https://remixicon.com/ -------------------------------

    // food icon
    protected const CONSUMABLE_ICON = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="#eeeeee" d="M0 0h24v24H0z"/><path d="M21 2v20h-2v-8h-3V7a5 5 0 0 1 5-5zM9 13.9V22H7v-8.1A5.002 5.002 0 0 1 3 9V3h2v7h2V3h2v7h2V3h2v6a5.002 5.002 0 0 1-4 4.9z"/></svg>';

    // armor icon 
    protected const ARMOR_ICON = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="#eeeeee" d="M0 0h24v24H0z"/><path d="M3.783 2.826L12 1l8.217 1.826a1 1 0 0 1 .783.976v9.987a6 6 0 0 1-2.672 4.992L12 23l-6.328-4.219A6 6 0 0 1 3 13.79V3.802a1 1 0 0 1 .783-.976z"/></svg>';

    // weapon icon
    protected const WEAPON_ICON = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="#eeeeee" d="M0 0h24v24H0z"/><path d="M7.05 13.406l3.534 3.536-1.413 1.414 1.415 1.415-1.414 1.414-2.475-2.475-2.829 2.829-1.414-1.414 2.829-2.83-2.475-2.474 1.414-1.414 1.414 1.413 1.413-1.414zM3 3l3.546.003 11.817 11.818 1.415-1.414 1.414 1.414-2.474 2.475 2.828 2.829-1.414 1.414-2.829-2.829-2.475 2.475-1.414-1.414 1.414-1.415L3.003 6.531 3 3zm14.457 0L21 3.003l.002 3.523-4.053 4.052-3.536-3.535L17.457 3z"/></svg>';
    
// -- RELATIONSHIPS -----------------------------------------------

    /**
     * @required by CanBeIngredient
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function ingredientForItems()
    {
        return $this->hasManyThrough( ItemRecipe::class, Requirement::class, 'item_id',  'creation_id');
    }
    
    /**
     * @required by CanBeIngredient
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function ingredientForPieces()
    {
        return $this->hasManyThrough( PieceRecipe::class, Requirement::class, 'item_id',  'creation_id');
    }

    /**
     * Need this so we can use it to check for recipes
     * 
     * items can have multiple recipes for their variants
     * e.g., Bronze and 5 Bronze bars
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipes()
    {
        return $this->hasMany( ItemRecipe::class, 'creation_id', 'id' ) ?? $this->hasMany( PieceRecipe::class, 'creation_id' ) ?? null;
    }

    /**
     * Need this so we can use it to check for sharedData
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sharedData()
    {
        return $this->belongsTo( SharedData::class );
    }

// -- MISC -----------------------------------------------

    // split string into array on uppercase letter and turn it into string
    public static function niceName( $name ) : string
    {
        return trim( implode( ' ', preg_split( '/(?=[A-Z])/', $name ) ) ) ?? $name;
    }

    public function imageUrl() : string
    {
        return !empty($this->getAttribute('image')) ? asset('storage/images/'.$this->image) : '';
    }

    public function image() : string
    {
        $image = !empty($this->getAttribute('image')) ? asset('storage/images/'.$this->image) : '';

        return '<img src="'.$image.'" alt="'.self::niceName($this->name).'">';
    }

    public function icon()
    {
        if($this->isArmor()){
            return Armor::ICON;
        }

        if($this->isWeapon()){
            return Weapon::ICON;
        }

        if($this->isConsumable()){
            return Consumable::ICON;
        }
        
        return null;
    }

    /**
     * category/type as string
     *
     * @return string   type/category
     * @throws \ErrorException
     */
    public function type() : string
    {
        return !empty($this->sharedData) ? $this->niceName( ItemType::toString( $this->sharedData->item_type ) ) : (!empty($this->category) ? $this->niceName( PieceCategory::toString( $this->category ) ) : '');
    }

    public function hasRecipes() : bool
    {
        return (!empty($this->recipes->filter()->all()));
    }

    public function hasSharedData() : bool
    {
        return (!empty($this->sharedData));
    }

    /*************************************
     * WEAPON METHODS
     */
    public function isWeapon() : bool
    {
        return null !== $this->sharedData && in_array( $this->sharedData->item_type, self::WEAPON_TYPES );
    }

    /*************************************
     * ARMOR METHODS
     */
    public function isArmor() : bool
    {
        return null !== $this->sharedData && in_array( $this->sharedData->item_type, self::ARMOR_TYPES );
    }

    /*************************************
     * FOOD METHODS
     */
    public function isConsumable() : bool
    {
        return null !== $this->sharedData && ( (int)$this->sharedData->item_type === ItemType::Consumable );
    }

    public function isFood() : bool
    {
        return $this->isConsumable();
    }

    /**
     * uniformity for description field
     *
     * @return ?string
     */
    public function description() : ?string
    {
        return ($this->hasSharedData() ? $this->sharedData->description : ( $this->description ?? ''));
    }
}
