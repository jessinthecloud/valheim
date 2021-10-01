<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weapon extends Item
{
    use HasFactory;

    public function attackEffect()
    {
        $effect = $this->sharedData->attackStatusEffect;

        return isset($effect) ? $effect->tooltip : null;
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
