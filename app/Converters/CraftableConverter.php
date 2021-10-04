<?php

namespace App\Converters;

abstract class CraftableConverter extends JsonConverter
{    
    abstract protected function attachDataToModel($data, $model);
}