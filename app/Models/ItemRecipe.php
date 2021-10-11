<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRecipe extends Recipe
{
    use HasFactory;

    // Indices of the converted json array that correspond to
    // relationships and should not be directly inserted
    // index_name => relationFunctionName ; to allow array_intersect_key comparison
    public const RELATION_INDICES = [
        'item' => 'creation',
        'requirements' => 'requirements',
        'raw_crafting_station_name' => 'craftingDevice',
        'raw_repair_station_name' => 'repairStation',
    ];

    // item this recipe creates
    public function creation()
    {
        return $this->belongsTo(Item::class, 'creation_id');
    }

    /**
     * tool that makes the piece
     *
     * @return Eloquent Relationship or Collection of PieceTable
     */
    public function craftingDevice()
    {
        return $this->craftingStation();
    }
}
