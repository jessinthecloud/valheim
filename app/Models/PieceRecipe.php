<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PieceRecipe extends Recipe
{
    use HasFactory;
    
    protected $table = 'piece_recipes';

    // Indices of the converted json array that correspond to
    // relationships and should not be directly inserted
    // index_name => relationFunctionName ; to allow array_intersect_key comparison
    public const RELATION_INDICES = [
        'piece_slug' => [
            'method' => 'creation',
            'class' => Piece::class,
            'relation' => 'associate',
        ],
        'requirements' => [
            'method' => 'requirements',
            'class' => Requirement::class,
            'relation' => 'attach',
        ],
        'raw_crafting_station_name' => [
            'method' => 'craftingStation',
            'class' => CraftingStation::class,
            'relation' => 'associate',
        ],
        'piece_table_true_name' => [
            'method' => 'craftingDevice',
            'class' => PieceTable::class,
            'relation' => 'associate',
        ],
    ];

    // piece this recipe creates
    public function creation()
    {
        return $this->belongsTo(Piece::class, 'creation_id');
    }

    /**
     * tool that makes the piece
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo Relationship or Collection of PieceTable
     */
    public function craftingDevice()
    {
        return $this->belongsTo(PieceTable::class, 'piece_table_id');
    }

    /**
     * tool required nearby to make the piece
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo Relationship or Collection of CraftingStation
     */
    public function craftingStation()
    {
        return $this->belongsTo(CraftingStation::class, 'crafting_station_id');
    }

    public function requirements()
    {
        return $this->belongsToMany(Requirement::class);
    }
}
