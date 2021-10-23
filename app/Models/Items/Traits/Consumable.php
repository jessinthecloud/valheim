<?php

namespace App\Models\Items\Traits;

trait Consumable
{
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