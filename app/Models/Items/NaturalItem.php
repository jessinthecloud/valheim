<?php

namespace App\Models\Items;

use App\Models\Items\Contracts\CanBeIngredient;
use Illuminate\Database\Eloquent\Builder;

class NaturalItem extends AbstractItem implements CanBeIngredient
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
        static::addGlobalScope('enabled', function (Builder $builder) {
            return $builder->doesntHave('recipes');
        });
    }

    public function type() : string
    {
        // TODO: Implement type() method.
    }
}