<?php

namespace App\Models;

use App\Enums\ItemType;

use App\Enums\PieceCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piece extends Craftable
{
    use HasFactory;

    public function recipeRequirements()
    {
        return $this->belongsToManyThrough(PieceRecipe::class, Requirement::class);
    }
    
/////////////////////////////////////////////////////////////////

    public function relatedItems()
    {
        return Requirement::whereHas('item', function($query){
            $query->where('item_id', $this->item->id);
        })
        ->orWhereHas('pieces', function($query){
            $query->whereHas('requirements',  function($query){
                $query->where('item_id', $this->item->id);
            });
        })
        ->get()->unique('item_id');
    }

    /**
     * shared_data item_type as string
     *
     * @return string   item type
     */
    public function type() : string
    {
        return $this->name_EN(PieceCategory::toString($this->category));
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
}
