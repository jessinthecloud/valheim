<?php

namespace App\Models;

use App\Models\ItemSharedData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    public string $name; // internalID - as seen on wiki
    // public ItemData $data; // ItemData -- not used, is not instanced from game
    public $shared_data; // ItemSharedData

    public function __construct(string $name='', ItemSharedData $shared_data=null)
    {
        $this->name = $name;
        $this->shared_data = $shared_data;
    }
}
