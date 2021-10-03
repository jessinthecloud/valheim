<?php

namespace App\Converters;

use Illuminate\Support\Str;

class CraftingDeviceConverter extends JsonConverter
{     
    public function __construct(string $class)
    {
        parent::__construct($class);
        
        $this->file = $this->filepath.'\\'.Str::kebab(Str::afterLast($this->class, '\\')).'-list.json';
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