<?php

namespace App\Models\Craftables;

use App\Enums\PieceCategory;
use App\Models\Recipes\PieceRecipe;
use App\Models\Recipes\Requirement;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Piece extends Craftable
{
    use HasFactory;

    protected $table = 'pieces';

// -- RELATIONSHIPS -----------------------------------------------

    /**
     * @required by Craftable
     *
     * @return mixed
     */
    public function requirementFor()
    {
        return $this->hasManyThrough( PieceRecipe::class, Requirement::class );
    }

    public function recipes()
    {
        return $this->belongsTo(PieceRecipe::class, 'creation_id');
    }

// -- SCOPES -----------------------------------------------------


// -- MISC -----------------------------------------------

    /**
     * piece category as string
     *
     * @return string   item type
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
