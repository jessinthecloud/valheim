<?php

namespace App\Models\Items\Traits;

trait Consumable
{
    public function health()
    {
        return $this->sharedData->food;
    }

    public function stamina()
    {
        return $this->sharedData->food_stamina;
    }

    public function healthRegen()
    {
        return $this->sharedData->food_regen;
    }

// -- CALCULATIONS -----------------------------------------------

    public function duration()
    {
        return ( $this->sharedData->food_burn_time / 60 ) . ' minutes';
    }
}