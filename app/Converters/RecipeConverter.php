<?php

namespace App\Converters;

abstract class RecipeConverter extends JsonConverter
{    
    abstract protected function attachCreation($data, $model);
    
    // allow for crafting or repair station
    abstract protected function attachCraftingDevice($data, $model, string $device_class);
    
    abstract protected function attachRequirements($data, $model);
}