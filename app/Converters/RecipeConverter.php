<?php

namespace App\Converters;

abstract class RecipeConverter extends ModelConverter
{
   
    abstract protected function attachCreation($value, $model, $data);
    
    // allow for crafting or repair station
    abstract protected function attachCraftingDevice($value, $model, string $device_class, $data);
    
    abstract protected function attachRequirements($data, $model);
}