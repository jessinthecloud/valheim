<?php

namespace App\Models\Recipes;

use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Tools\CraftingStation;
use App\Models\Tools\RepairStation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemRecipe extends Recipe
{
    use HasFactory;
    
    protected $table = 'item_recipes';

    // Indices of the converted json array that correspond to
    // relationships and should not be directly inserted
    // index_name => relationFunctionName ; to allow array_intersect_key comparison
    public const RELATION_INDICES = [
        'item_slug' => [
            'method' => 'creation',
            'class' => CraftableItem::class,
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
            'class' => RepairStation::class,
            'relation' => 'associate',
        ],
    ];

// -- RELATIONSHIPS -----------------------------------------------

    // item this recipe creates
    public function creation()
    {
        return $this->belongsTo(CraftableItem::class, 'creation_id');
    }

    public function requirements()
    {
        return $this->belongsToMany(Requirement::class);
    }

    /**
     * tool that makes the item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * Relationship or Collection of PieceTable
     */
    public function craftingDevice()
    {
        return $this->craftingStation();
    }

// -- MISC -----------------------------------------------

    public function icon() : string
    {
        return null !== $this->creation ? $this->creation->icon() : '';
    }
}
