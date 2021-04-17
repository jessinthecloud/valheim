<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CraftingStation extends Model
{
    use HasFactory;

    public string $name;
    public string $name_EN;

    public function __construct(/*$name, $name_EN*/)
    {
        /*$this->name = $name;
        $this->name_EN = $name_EN;*/
    }
}
