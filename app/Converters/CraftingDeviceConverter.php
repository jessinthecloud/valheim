<?php

namespace App\Converters;

use Illuminate\Support\Str;

/**
 * Manages conversion of CraftingStation, PieceTable, etc
 */
class CraftingDeviceConverter extends JsonConverter
{     
    public function __construct(string $class)
    {
        parent::__construct($class);
    }
    
    public function create()
    {
        // insert into table
        $this->data = $this->data->map(function($entity){
            
            $data = $this->convertNames($entity);
            
            $model = $this->class::firstOrCreate(
                $data
            );
            
            return $data;
        });
    }
}