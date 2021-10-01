<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRecipe extends Recipe
{
    use HasFactory;

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
