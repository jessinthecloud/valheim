<?php

namespace App\Models\Items\Craftables\Pieces;

use App\Enums\PieceCategory;
use App\Models\Items\Contracts\IsCategorizable;
use App\Models\Items\Contracts\IsCraftable;
use App\Models\Items\Contracts\IsItem;
use App\Models\Items\Item;
use App\Models\Items\Traits\HasRecipe;
use App\Models\Recipes\PieceRecipe;
use App\Models\Recipes\Requirement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piece extends Item implements IsItem, IsCraftable, IsCategorizable
{
    use HasFactory, HasRecipe;

    protected $table = 'pieces';

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

// -- RELATIONSHIPS -----------------------------------------------

    /**
     * items can have multiple recipes for their variants
     * e.g., Bronze and 5 Bronze bars
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipes()
    {
        return $this->hasMany( PieceRecipe::class, 'creation_id' );
    }

// -- SCOPES -----------------------------------------------------


// -- MISC -----------------------------------------------

    /**
     * piece category as string
     *
     * @return string   item type
     * @throws \ErrorException
     */
    public function type() : string
    {
        return $this->niceName( PieceCategory::toString( $this->category ) );
    }

    public function isFurniture() : bool
    {
        return $this->category === PieceCategory::Furniture;
    }

    public function isForBuilding() : bool
    {
        return $this->category === PieceCategory::Building;
    }

    public function isForCrafting() : bool
    {
        return $this->category === PieceCategory::Crafting;
    }

    public function relatedItems()
    {
        return Requirement::whereHas( 'item', function ( $query ) {
            $query->where( 'item_id', $this->item->id );
        } )
            ->orWhereHas( 'pieces', function ( $query ) {
                $query->whereHas( 'requirements', function ( $query ) {
                    $query->where( 'item_id', $this->item->id );
                } );
            } )
            ->get()->unique( 'item_id' );
    }

    public function isWeapon() : bool
    {
        return false;
    }

    public function isArmor() : bool
    {
        return false;
    }

    public function isConsumable() : bool
    {
        return false;
    }
    
    public function isFood() : bool
    {
        return false;
    }
    
    // TODO: calculate quality / levels of pieces ? based on $this->is_upgrade
    
}
