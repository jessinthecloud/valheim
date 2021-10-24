<?php

namespace App\Models\Items\Contracts;

interface CanBeIngredient
{
    public function ingredientForItems();
    
    public function ingredientForPieces();
}