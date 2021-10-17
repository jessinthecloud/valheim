<?php

namespace App\Models\Items\Craftables\Items;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Weapon extends CraftableItem
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
        static::addGlobalScope('weapon', function (Builder $builder) {
            $builder->weapon();
        });
    }

// -- MISC -----------------------------------------------------

    public function attackEffect()
    {
        $effect = $this->sharedData->attackStatusEffect;

        return isset( $effect ) ? $effect->tooltip : null;
    }

    public function attack()
    {
        return $this->sharedData->attack_force;
    }

    public function backstab()
    {
        return $this->sharedData->backstab_bonus;
    }

    public function block()
    {
        return $this->sharedData->block_power;
    }

    public function armor()
    {
        return $this->sharedData->armor;
    }
}
