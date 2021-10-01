<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Armor extends Item
{
    use HasFactory;

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

    public function movementEffect()
    {
        return abs($this->sharedData->movement_modifier).' '.($this->sharedData->movement_modifier > 1 ? 'Faster' : 'Slower');
    }
}
