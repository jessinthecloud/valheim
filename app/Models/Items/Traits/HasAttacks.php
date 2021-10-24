<?php

namespace App\Models\Items\Traits;

trait HasAttacks
{
    public function attack()
    {
        return $this->sharedData->attack_force;
    }

    public function backstab()
    {
        return $this->sharedData->backstab_bonus;
    }

    /* 
    public function block()
    {
        return $this->sharedData->block_power;
    } 
    
    public function armor()
    {
        return $this->sharedData->armor;
    }
    */

}