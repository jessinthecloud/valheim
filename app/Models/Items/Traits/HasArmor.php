<?php

namespace App\Models\Items\Traits;

trait HasArmor
{
    public function armor()
    {
        return $this->sharedData->armor;
    }

    public function block()
    {
        return $this->sharedData->block_power;
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
        ) . 'x ' . ( $this->sharedData->movement_modifier > 1 ? 'Faster' : 'Slower' );
    }
}