<?php

namespace App\Models\Tools;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class CraftingDevice extends Model
{
    use HasFactory;
    
    // has many through recipe
    abstract public function craftables();
    
}
