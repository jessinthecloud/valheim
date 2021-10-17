<?php

namespace App\Models\Craftables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Armor extends Item
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
        static::addGlobalScope('armor', function (Builder $builder) {
            $builder->armor();
        });
    }

// -- MISC -----------------------------------------------------

    public function armor()
    {
        return $this->sharedData->armor;
    }

    public function armorPerLevel()
    {
        return $this->sharedData->armor_per_level;
    }

    public function deflection()
    {
        return $this->sharedData->deflection_force;
    }

    public function deflectionPerLevel()
    {
        return $this->sharedData->deflection_force_per_level;
    }

    public function movementModifier()
    {
        return $this->sharedData->movement_modifier;
    }
    
// -- CALCULATIONS -----------------------------------------------

    public function movementEffect()
    {
        return abs(
                $this->sharedData->movement_modifier
            ) . ' ' . ( $this->sharedData->movement_modifier > 1 ? 'Faster' : 'Slower' );
    }
}
