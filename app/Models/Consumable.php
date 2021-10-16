<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consumable extends Item
{
    use HasFactory;

    protected $table = 'items';

// -- SCOPES -----------------------------------------------------

    /**
     * Apply this scope to every query
     * made by this model
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('consumable', function (Builder $builder) {
            $builder->consumable();
        });
    }

    public function health()
    {
        return (int)$this->sharedData->food;
    }

    public function stamina()
    {
        return (int)$this->sharedData->food_stamina;
    }

    public function healthRegen()
    {
        return (int)$this->sharedData->food_regen;
    }

// -- CALCULATIONS -----------------------------------------------

    public function duration()
    {
        return ( (int)$this->sharedData->food_burn_time / 60 ) . ' minutes';
    }
}
