<?php

namespace App\Converters;

abstract class CraftableConverter extends JsonConverter
{

    public function convert()
    {
        // TODO: Implement convert() method.
    }
    
    abstract protected function attachData();
}