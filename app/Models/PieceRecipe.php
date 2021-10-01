<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PieceRecipe extends Recipe
{
    use HasFactory;

    // piece this recipe creates
    public function creation()
    {
        return $this->belongsTo(Piece::class, 'creation_id');
    }

    /**
     * tool that makes the piece
     *
     * @return Eloquent Relationship or Collection of PieceTable
     */
    public function craftingDevice()
    {
        return $this->belongsTo(PieceTable::class);
    }
}
