<?php

namespace App\Models\Items\Craftables\Pieces;

use App\Enums\PieceCategory;
use App\Models\Items\Contracts\IsCategorized;
use App\Models\Items\Contracts\IsCraftable;
use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Recipes\PieceRecipe;
use App\Models\Recipes\Requirement;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Piece extends CraftableItem implements IsCraftable, IsCategorized
{
    use HasFactory;

    protected $table = 'pieces';

// -- RELATIONSHIPS -----------------------------------------------

    /**
     *
     * @return mixed
     */
    public function requirementFor()
    {
        return $this->hasManyThrough( PieceRecipe::class, Requirement::class );
    }

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
}
