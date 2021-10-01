<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    use HasFactory;

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

    public function duration()
    {
        return ((int)$this->sharedData->food_burn_time/60).' minutes';
    }
}
