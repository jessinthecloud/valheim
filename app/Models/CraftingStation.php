<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CraftingStation extends CraftingDevice
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

    /*
        vars to remove if present in JSON
        -- prevents table and relationship checking
     */
    public static $ignore = [];

    public function craftables()
    {
        return $this->hasManyThrough(Item::class, ItemRecipe::class);
    }

    public function pieces()
    {
        return $this->hasMany(Piece::class);
    }

    public function slug()
    {
        return Str::slug($this->name);
    }

    public function itemRecipes()
    {
        return $this->hasMany(ItemRecipe::class);
    }

    public function pieceRecipes()
    {
        return $this->hasMany(PieceRecipe::class);
    }
}
