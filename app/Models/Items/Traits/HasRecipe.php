<?php

namespace App\Models\Items\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasRecipe
{
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
        static::addGlobalScope('enabled', function (Builder $builder) {
            return $builder->whereHas( 'recipes', function($query){
                $query->where('enabled', 1);
            });
        });
    }
}