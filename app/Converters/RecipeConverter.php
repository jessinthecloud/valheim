<?php

namespace App\Converters;

abstract class RecipeConverter extends JsonConverter
{

    public function convert()
    {
        // TODO: Implement convert() method.
    }
    
    abstract protected function attachCreation();
    
    abstract protected function attachCraftingDevice();
    
    abstract protected function attachRequirements();
}