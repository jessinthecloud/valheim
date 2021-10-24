<?php

namespace App\Models\Items;

use App\Models\Items\Contracts\CanBeIngredient;
use App\Models\Items\Contracts\IsItem;
use App\Models\Items\Traits\HasArmor;
use App\Models\Items\Traits\HasAttacks;
use App\Models\Items\Traits\HasSharedData;
use Illuminate\Database\Eloquent\Builder;

class NaturalItem extends Item implements IsItem, CanBeIngredient
{
    use HasSharedData, HasAttacks, HasArmor;
    
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

    /**
     * Need this so we can use it to check for sharedData
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sharedData()
    {
        return $this->belongsTo( SharedData::class );
    }
}