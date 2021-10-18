<?php

namespace App\Models\Items;

use App\Models\Items\Contracts\CanBeIngredient;
use Illuminate\Database\Eloquent\Builder;

class NaturalItem extends Item
{
    protected $table = 'items';

    // -- GLOBAL SCOPES -----------------------------------------------------

    /**
     * Apply this scope to every query
     * made by this model
     *
     * Get only enabled recipe items (or items with no recipe)
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('natural', function (Builder $builder) {
            return $builder->doesntHave('recipes');
        });
    }
}