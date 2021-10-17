<?php

namespace App\Models\Craftables\Items;

use App\Enums\ItemType;
use App\Http\ImageFetcher;
use App\Models\Craftables\Craftable;
use App\Models\Recipes\ItemRecipe;
use App\Models\Recipes\Requirement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Craftable
{
    use HasFactory;

    protected $table = 'items';

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
    protected $with = [
        'sharedData',
    ];

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

    // Indices of the converted json array that correspond to
    // relationships and should not be directly inserted
    // index_name => relationFunctionName ; to allow array_intersect_key comparison
    public const RELATION_INDICES = [
        'shared_data' => [
            'method' => 'sharedData',
            'class' => SharedData::class,
            'relation' => 'associate',
        ],
    ];

// -- GLOBAL SCOPES -----------------------------------------------------

    /**
     * Apply this scope to every query
     * made by this model
     * 
     * Get only enabled recipe items (or items with no recipe)
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('enabled', function (Builder $builder) {
            return $builder->whereHas( 'recipes', function($query){
                $query->where('enabled', 1);
            })
            ->orDoesntHave('recipes')
            ;
        });
    }

// -- RELATIONSHIPS -----------------------------------------------

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

    public function requirements()
    {
        return $this->hasMany(Requirement::class, 'item_id');
    }

    /**
     * @required by Craftable
     *
     * @return mixed
     */
    public function requirementFor()
    {
        return $this->hasManyThrough( ItemRecipe::class, Requirement::class, 'item_id',  'creation_id');
    }

    public function sharedData()
    {
        return $this->belongsTo( SharedData::class );
    }

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

// -- MISC -----------------------------------------------

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

    public function image( ImageFetcher $fetcher )
    {
        return $fetcher->fetchImageHtmlString( Str::snake( $this->name ) );
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


}
