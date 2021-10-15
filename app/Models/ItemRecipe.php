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
        'item_slug' => [
            'method' => 'creation',
            'class' => Item::class,
            'relation' => 'associate',
        ],
        'requirements' => [
            'method' => 'requirements',
            'class' => Requirement::class,
            'relation' => 'attach',
        ],
        'raw_crafting_station_name' => [
            'method' => 'craftingDevice',
            'class' => CraftingStation::class,
            'relation' => 'associate',
        ],
        'raw_repair_station_name' => [
            'method' => 'repairStation',
            'class' => CraftingStation::class,
            'relation' => 'associate',
        ],
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
